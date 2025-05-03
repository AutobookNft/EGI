<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    //
}
<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Controller for managing EGI reservations.
 *
 * Handles the reservation process for EGIs, allowing users to express interest
 * in purchasing an EGI when it becomes available for minting.
 *
 * --- Core Logic ---
 * 1. Creates new reservations with appropriate expiration times
 * 2. Validates reservation requests to ensure EGI availability
 * 3. Manages different reservation types (weak/strong)
 * 4. Provides both web and API endpoints for reservation functionality
 * 5. Enforces reservation limits and business rules
 * --- End Core Logic ---
 *
 * @package App\Http\Controllers
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class ReservationController extends Controller
{
    /**
     * Create a new reservation for an EGI.
     *
     * Handles both weak (wallet only, 24-hour expiration)
     * and strong (wallet + personal info, valid until mint) reservations.
     *
     * @param Request $request The HTTP request
     * @param Egi $egi The EGI to reserve
     * @return \Illuminate\Http\RedirectResponse Redirect to the EGI page
     */
    public function reserve(Request $request, Egi $egi)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'reservation_type' => 'required|in:weak,strong',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $type = $request->input('reservation_type');

        // Check if the EGI is available for reservation
        if ($egi->mint) {
            return redirect()->back()
                ->with('error', 'This EGI has already been minted and is not available for reservation.');
        }

        // Check if the user already has a reservation for this EGI
        $existingReservation = Reservation::where('user_id', $user->id)
            ->where('egi_id', $egi->id)
            ->first();

        if ($existingReservation) {
            return redirect()->back()
                ->with('error', 'You already have a reservation for this EGI.');
        }

        // Create new reservation
        $reservation = new Reservation();
        $reservation->user_id = $user->id;
        $reservation->egi_id = $egi->id;
        $reservation->type = $type;
        $reservation->status = 'active';

        // Set expiration based on type
        if ($type === 'weak') {
            $reservation->expires_at = Carbon::now()->addHours(24);
        } else {
            // Strong reservations don't expire until mint
            $reservation->expires_at = null;

            // For strong reservations, may need additional data
            if ($request->has('contact_data')) {
                $reservation->contact_data = $request->input('contact_data');
            }
        }

        $reservation->save();

        // Redirect back with success message
        return redirect()->back()
            ->with('success', 'Your reservation has been created successfully.');
    }

    /**
     * API endpoint to create a reservation.
     *
     * Returns JSON response with the reservation status.
     *
     * @param Request $request The HTTP request
     * @param Egi $egi The EGI to reserve
     * @return \Illuminate\Http\JsonResponse JSON response with reservation status
     */
    public function apiReserve(Request $request, Egi $egi)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'reservation_type' => 'required|in:weak,strong',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reservation type.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $type = $request->input('reservation_type');

        // Check if the EGI is available for reservation
        if ($egi->mint) {
            return response()->json([
                'success' => false,
                'message' => 'This EGI has already been minted and is not available for reservation.'
            ], 400);
        }

        // Check if the user already has a reservation for this EGI
        $existingReservation = Reservation::where('user_id', $user->id)
            ->where('egi_id', $egi->id)
            ->first();

        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a reservation for this EGI.'
            ], 400);
        }

        // Create new reservation
        $reservation = new Reservation();
        $reservation->user_id = $user->id;
        $reservation->egi_id = $egi->id;
        $reservation->type = $type;
        $reservation->status = 'active';

        // Set expiration based on type
        if ($type === 'weak') {
            $reservation->expires_at = Carbon::now()->addHours(24);
        } else {
            // Strong reservations don't expire until mint
            $reservation->expires_at = null;

            // For strong reservations, may need additional data
            if ($request->has('contact_data')) {
                $reservation->contact_data = $request->input('contact_data');
            }
        }

        $reservation->save();

        // Get the updated reservation count
        $reservationsCount = Reservation::where('egi_id', $egi->id)
            ->where('status', 'active')
            ->count();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Your reservation has been created successfully.',
            'reservation' => $reservation,
            'reservations_count' => $reservationsCount,
            'reservation_type' => $type,
            'expiration' => $reservation->expires_at
        ]);
    }

    /**
     * Cancel a reservation.
     *
     * @param Reservation $reservation The reservation to cancel
     * @return \Illuminate\Http\RedirectResponse Redirect to the EGI page
     */
    public function cancel(Reservation $reservation)
    {
        // Ensure the user owns this reservation
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'You do not have permission to cancel this reservation.');
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        return redirect()->back()
            ->with('success', 'Your reservation has been cancelled.');
    }

    /**
     * API endpoint to cancel a reservation.
     *
     * @param Reservation $reservation The reservation to cancel
     * @return \Illuminate\Http\JsonResponse JSON response with cancellation status
     */
    public function apiCancel(Reservation $reservation)
    {
        // Ensure the user owns this reservation
        if ($reservation->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to cancel this reservation.'
            ], 403);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        // Get the updated reservation count
        $reservationsCount = Reservation::where('egi_id', $reservation->egi_id)
            ->where('status', 'active')
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Your reservation has been cancelled.',
            'reservations_count' => $reservationsCount
        ]);
    }
}
