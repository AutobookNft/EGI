<?php

namespace App\Http\Controllers\User;

use App\Models\UserDocument;
use App\Http\Requests\User\StoreDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: User Documents Management
 * 🎯 Purpose: CRUD operations for user document uploads and verification
 * 🛡️ Privacy: Document storage with verification status tracking
 * 🧱 Core Logic: Upload, verify, download pattern with FegiAuth support
 */
class UserDocumentsController extends BaseUserDomainController
{
    /**
     * Display user documents list
     */
    public function index(): View|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can access documents
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('manage_own_documents')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            $documents = $user->documents()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $this->logger->info('[User Documents] Documents index accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'documents_count' => $documents->total()
            ]);

            return view('user.documents.index', compact('user', 'documents'));

        } catch (\Exception $e) {
            return $this->handleError('USER_DOCUMENTS_INDEX_FAILED', [
                'action' => 'index'
            ], $e);
        }
    }

    /**
     * Show document upload form
     */
    public function create(): View|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can upload documents
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('upload_identity_documents')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            $documentTypes = [
                'identity_card' => __('user_documents.type_identity_card'),
                'passport' => __('user_documents.type_passport'),
                'driving_license' => __('user_documents.type_driving_license'),
                'tax_code' => __('user_documents.type_tax_code'),
                'vat_certificate' => __('user_documents.type_vat_certificate'),
                'business_registration' => __('user_documents.type_business_registration'),
                'other' => __('user_documents.type_other')
            ];

            $this->logger->info('[User Documents] Upload form accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType()
            ]);

            return view('user.documents.create', compact('user', 'documentTypes'));

        } catch (\Exception $e) {
            return $this->handleError('USER_DOCUMENTS_CREATE_FAILED', [
                'action' => 'create_form'
            ], $e);
        }
    }

    /**
     * Store uploaded document
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can upload documents
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('upload_identity_documents')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();
            $file = $request->file('document');
            $validated = $request->validated();

            // Generate secure filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('user_documents/' . $user->id, $filename, 'private');

            // Create document record
            $document = UserDocument::create([
                'user_id' => $user->id,
                'document_type' => $validated['document_type'],
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $validated['description'] ?? null,
                'verification_status' => 'pending',
                'uploaded_at' => now(),
            ]);

            // Log upload for audit trail
            $this->logUserAction('document_uploaded', [
                'document_id' => $document->id,
                'document_type' => $validated['document_type'],
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ], 'document_management');

            $this->logger->info('[User Documents] Document uploaded successfully', [
                'user_id' => $user->id,
                'document_id' => $document->id,
                'document_type' => $validated['document_type'],
                'auth_type' => FegiAuth::getAuthType()
            ]);

            return redirect()->route('user.documents.index')
                ->with('success', __('user_documents.upload_success'));

        } catch (\Exception $e) {
            return $this->handleError('USER_DOCUMENT_UPLOAD_FAILED', [
                'action' => 'store',
                'document_type' => $request->input('document_type'),
            ], $e);
        }
    }

    /**
     * Show document details
     */
    public function show(UserDocument $document): View|RedirectResponse
    {
        try {
            // FegiAuth check
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            $user = FegiAuth::user();

            // Ensure user owns the document
            if ($document->user_id !== $user->id) {
                abort(403, __('user_documents.access_denied_document'));
            }

            if (!$this->checkWeakAuthAccess('manage_own_documents')) {
                abort(403, __('user_domain.access_denied'));
            }

            $this->logger->info('[User Documents] Document details viewed', [
                'user_id' => $user->id,
                'document_id' => $document->id,
                'auth_type' => FegiAuth::getAuthType()
            ]);

            return view('user.documents.show', compact('user', 'document'));

        } catch (\Exception $e) {
            return $this->handleError('USER_DOCUMENT_SHOW_FAILED', [
                'action' => 'show',
                'document_id' => $document->id ?? null,
            ], $e);
        }
    }

    /**
     * Download document file
     */
    public function download(UserDocument $document): Response|RedirectResponse
    {
        try {
            // FegiAuth check
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            $user = FegiAuth::user();

            // Ensure user owns the document
            if ($document->user_id !== $user->id) {
                abort(403, __('user_documents.access_denied_document'));
            }

            if (!$this->checkWeakAuthAccess('download_own_documents')) {
                abort(403, __('user_domain.access_denied'));
            }

            // Check file exists
            if (!Storage::disk('private')->exists($document->file_path)) {
                return $this->handleError('USER_DOCUMENT_FILE_NOT_FOUND', [
                    'document_id' => $document->id,
                    'file_path' => $document->file_path
                ]);
            }

            // Log download for audit trail
            $this->logUserAction('document_downloaded', [
                'document_id' => $document->id,
                'document_type' => $document->document_type,
            ], 'document_management');

            $this->logger->info('[User Documents] Document downloaded', [
                'user_id' => $user->id,
                'document_id' => $document->id,
                'auth_type' => FegiAuth::getAuthType()
            ]);

            return Storage::disk('private')->download(
                $document->file_path,
                $document->original_name
            );

        } catch (\Exception $e) {
            return $this->handleError('USER_DOCUMENT_DOWNLOAD_FAILED', [
                'action' => 'download',
                'document_id' => $document->id ?? null,
            ], $e);
        }
    }

    /**
     * Delete document
     */
    public function destroy(UserDocument $document): RedirectResponse
    {
        try {
            // FegiAuth check
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            $user = FegiAuth::user();

            // Ensure user owns the document
            if ($document->user_id !== $user->id) {
                abort(403, __('user_documents.access_denied_document'));
            }

            if (!$this->checkWeakAuthAccess('manage_own_documents')) {
                abort(403, __('user_domain.access_denied'));
            }

            // Don't allow deletion of verified documents
            if ($document->verification_status === 'verified') {
                return redirect()->route('user.documents.index')
                    ->with('error', __('user_documents.cannot_delete_verified'));
            }

            // Delete file from storage
            if (Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            // Log deletion for audit trail
            $this->logUserAction('document_deleted', [
                'document_id' => $document->id,
                'document_type' => $document->document_type,
                'verification_status' => $document->verification_status,
            ], 'document_management');

            // Delete record
            $document->delete();

            $this->logger->info('[User Documents] Document deleted', [
                'user_id' => $user->id,
                'document_id' => $document->id,
                'auth_type' => FegiAuth::getAuthType()
            ]);

            return redirect()->route('user.documents.index')
                ->with('success', __('user_documents.delete_success'));

        } catch (\Exception $e) {
            return $this->handleError('USER_DOCUMENT_DELETE_FAILED', [
                'action' => 'destroy',
                'document_id' => $document->id ?? null,
            ], $e);
        }
    }
}
