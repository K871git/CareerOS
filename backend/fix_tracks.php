<?php

use App\Models\LearningTrack;
use App\Models\Subject;

$fe = LearningTrack::where('slug', 'frontend-engineering')->value('id');
$be = LearningTrack::where('slug', 'backend-engineering')->value('id');

Subject::whereIn('slug', ['javascript', 'react'])->update(['learning_track_id' => $fe]);
Subject::whereIn('slug', ['laravel', 'python'])->update(['learning_track_id' => $be]);

echo "Done. Frontend ID=$fe, Backend ID=$be\n";
