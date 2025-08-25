<?php
namespace Database\Seeds;

/**
 * Main database seeder
 * Runs all individual seeders in proper order
 */
class DatabaseSeeder {
    //Run all seeders
    public function run(): void {
        echo "Starting database seeding...<br><br>";

        // Run users first (projects depend on users)
        $usersSeeder = new UsersSeeder();
        $usersSeeder->truncate();
        $usersSeeder->run();

        // Then run projects
        $projectsSeeder = new ProjectsSeeder();
        $projectsSeeder->truncate();
        $projectsSeeder->run();

        echo "<br>Database seeding completed successfully!<br>";
    }

    //Run seeders without truncating (for additional data)
    public function runWithoutTruncate(): void {
        echo "Adding additional data without truncating...<br><br>";

        $usersSeeder = new UsersSeeder();
        $usersSeeder->run();

        $projectsSeeder = new ProjectsSeeder();
        $projectsSeeder->run();

        echo "<br>Additional data added successfully!<br>";
    }
}