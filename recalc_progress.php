<?php

use App\Models\Project;

foreach (Project::all() as $project) {
    $newProgress = $project->calculateProgressFromMilestones();
    $project->update(['progress_percentage' => $newProgress]);
    echo $project->name . ': ' . $newProgress . '%' . PHP_EOL;
}

echo 'Done!' . PHP_EOL;
