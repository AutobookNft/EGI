<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TestAvatarUpload extends Command
{
    protected $signature = 'test:avatar-upload';
    protected $description = 'Test avatar upload functionality for commissioners';

    public function handle()
    {
        $this->info('🔍 Testing Avatar Upload Functionality');

        // Find or create commissioner
        $commissioner = User::where('email', 'commissioner.test@florenceegi.com')->first();
        
        if (!$commissioner) {
            $this->error('❌ Commissioner not found. Run test:commissioner-display first.');
            return;
        }

        // Create a test avatar file (using existing public images or create simple file)
        $testImagePath = storage_path('app/test-avatar.png');
        if (!file_exists($testImagePath)) {
            // Try to use an existing PNG image from public folder
            $publicImages = [
                public_path('images/logo/logo_t.png'),
                public_path('images/logo/logo_2.png'),
                public_path('images/logo/logo_k_sfondo.png')
            ];
            
            $foundImage = null;
            foreach ($publicImages as $imagePath) {
                if (file_exists($imagePath)) {
                    $foundImage = $imagePath;
                    break;
                }
            }
            
            if ($foundImage) {
                copy($foundImage, $testImagePath);
                $this->info('📸 Using existing PNG image for test: ' . basename($foundImage));
            } else {
                // Skip test if no valid image found
                $this->error('❌ No valid PNG images found for testing');
                return;
            }
        }

        // Add media to commissioner using the correct collection
        try {
            // Clear existing media first
            $commissioner->clearMediaCollection('profile_images');
            
            // Add media to the profile_images collection (as used by User model)
            $media = $commissioner
                ->addMedia($testImagePath)
                ->toMediaCollection('profile_images');
            
            // Set this as the current profile image using the User model method
            $commissioner->update([
                'profile_photo_path' => $media->file_name
            ]);
            
            $this->info('✅ Avatar uploaded successfully to profile_images collection');
            $this->info('📂 Media ID: ' . $media->id);
            $this->info('🔗 Media URL: ' . $media->getUrl());
            $this->info('👤 Profile Photo URL: ' . $commissioner->profile_photo_url);

            // Test the display function
            $display = formatActivatorDisplay($commissioner);
            
            $this->info('🎯 Display Test Results:');
            $this->line('  Name: ' . $display['name']);
            $this->line('  Is Commissioner: ' . ($display['is_commissioner'] ? 'YES' : 'NO'));
            $this->line('  Avatar URL: ' . ($display['avatar'] ?: 'NULL'));
            
            if ($display['avatar']) {
                $this->info('✅ Avatar is properly loaded in display function!');
            } else {
                $this->error('❌ Avatar not loaded in display function');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error uploading avatar: ' . $e->getMessage());
        }

        // Clean up test file
        if (file_exists($testImagePath)) {
            unlink($testImagePath);
        }
    }
}
