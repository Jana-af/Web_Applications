<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\File;
use App\Models\User;
use Tests\TestCase;

// class CheckInConcurrencyTest extends TestCase
// {
//     use RefreshDatabase;

//     /**
//      * Test that concurrent check-ins respect the lockForUpdate constraint.
//      */
//     public function test_concurrent_check_in()
//     {
//         // Create users and files
//         $user1 = User::factory()->create();
//         $user2 = User::factory()->create();
//         $files = File::factory()->count(3)->create();

//         // Simulate two users checking in at the same time
//         // Run both actions concurrently using separate database connections
//         $this->simulateCheckInConcurrently($files->pluck('id'), $user1->id, $user2->id);

//         // Check the outcome
//         $reservedByUser1 = File::where('current_reserver_id', $user1->id)->count();
//         $reservedByUser2 = File::where('current_reserver_id', $user2->id)->count();

//         // Assert that all files have been reserved, but only one user succeeded
//         $this->assertEquals(3, $reservedByUser1 + $reservedByUser2, 'Total reserved files should be 3.');
//         $this->assertTrue($reservedByUser1 === 0 || $reservedByUser2 === 0, 'Only one user should succeed in reserving the files.');

//         print("Reserved by User 1: $reservedByUser1\n");
//         print("Reserved by User 2: $reservedByUser2\n");
//     }

//     /**
//      * Simulate concurrent check-in attempts for two users.
//      *
//      * @param \Illuminate\Support\Collection $fileIds
//      * @param int $user1Id
//      * @param int $user2Id
//      */
//     private function simulateCheckInConcurrently($fileIds, $user1Id, $user2Id)
//     {
//         // Start two separate transactions for two users
//         $thread1 = $this->startTransactionForUser($fileIds, $user1Id);
//         $thread2 = $this->startTransactionForUser($fileIds, $user2Id);

//         // Sleep to simulate time taken during the check-in process and let both transactions run
//         sleep(3);  // Adjust time to match your desired delay

//         // Wait for both transactions to finish
//         $thread1->wait();
//         $thread2->wait();
//     }

//     /**
//      * Start a database transaction for a user to simulate a check-in.
//      *
//      * @param \Illuminate\Support\Collection $fileIds
//      * @param int $userId
//      * @return \parallel\Runtime
//      */
//     private function startTransactionForUser($fileIds, $userId)
//     {
//         $runtime = new \parallel\Runtime();

//         // Run the check-in process in the separate thread
//         $runtime->run(function () use ($fileIds, $userId) {
//             DB::transaction(function () use ($fileIds, $userId) {
//                 // Lock files for update
//                 $files = File::lockForUpdate()->whereIn('id', $fileIds)->get();

//                 sleep(2);  // Simulate processing delay

//                 // Process the check-in for each file
//                 foreach ($files as $file) {
//                     $file->current_reserver_id = $userId;
//                     $file->status = 'RESERVED';
//                     $file->save();
//                 }
//             });
//         });

//         return $runtime;
//     }
// }
