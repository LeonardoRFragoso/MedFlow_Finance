<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Upload;
use App\Models\Role;
use App\Policies\UploadPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_any_upload()
    {
        $clinic = Clinic::factory()->create();
        $admin = User::factory()->create(['clinic_id' => $clinic->id]);
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        
        $policy = new UploadPolicy();
        
        $this->assertTrue($policy->viewAny($admin));
    }

    /** @test */
    public function user_can_view_own_upload()
    {
        $clinic = Clinic::factory()->create();
        $user = User::factory()->create(['clinic_id' => $clinic->id]);
        $upload = Upload::factory()->create([
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
        ]);
        
        $policy = new UploadPolicy();
        
        $this->assertTrue($policy->view($user, $upload));
    }

    /** @test */
    public function user_cannot_view_upload_from_other_clinic()
    {
        $clinic1 = Clinic::factory()->create();
        $clinic2 = Clinic::factory()->create();
        
        $user = User::factory()->create(['clinic_id' => $clinic1->id]);
        $upload = Upload::factory()->create(['clinic_id' => $clinic2->id]);
        
        $policy = new UploadPolicy();
        
        $this->assertFalse($policy->view($user, $upload));
    }

    /** @test */
    public function user_cannot_delete_processing_upload()
    {
        $clinic = Clinic::factory()->create();
        $user = User::factory()->create(['clinic_id' => $clinic->id]);
        $upload = Upload::factory()->create([
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'status' => 'processing',
        ]);
        
        $policy = new UploadPolicy();
        
        $this->assertFalse($policy->delete($user, $upload));
    }

    /** @test */
    public function user_can_delete_failed_upload()
    {
        $clinic = Clinic::factory()->create();
        $user = User::factory()->create(['clinic_id' => $clinic->id]);
        $upload = Upload::factory()->create([
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'status' => 'failed',
        ]);
        
        $policy = new UploadPolicy();
        
        $this->assertTrue($policy->delete($user, $upload));
    }
}
