#!/bin/bash
set -e

echo "==> Starting server on port $PORT..."
php artisan serve --host 0.0.0.0 --port "$PORT" &
SERVER_PID=$!

echo "==> Running migrations..."
php artisan migrate --force

if [ "$FORCE_SEED" = "true" ]; then
    echo "==> FORCE_SEED enabled — seeding each class in its own process..."

    SEEDERS=(
        "SkillSeeder"
        "PhpPracticeSeeder"
        "PhpJuniorQuestionsSeeder"
        "PhpIntermediateQuestionsSeeder"
        "PhpAdvancedQuestionsSeeder"
        "JsJuniorQuestionsSeeder"
        "JsIntermediateQuestionsSeeder"
        "JsAdvancedQuestionsSeeder"
        "LaravelJuniorQuestionsSeeder"
        "LaravelIntermediateQuestionsSeeder"
        "LaravelAdvancedQuestionsSeeder"
        "ReactJuniorQuestionsSeeder"
        "ReactIntermediateQuestionsSeeder"
        "ReactAdvancedQuestionsSeeder"
        "PythonJuniorQuestionsSeeder"
        "PythonIntermediateQuestionsSeeder"
        "PythonAdvancedQuestionsSeeder"
        "LearningTrackSeeder"
        "SubjectSeeder"
        "TopicSeeder"
        "LessonSeeder"
        "QuestionSeeder"
        "QuestionOptionSeeder"
        "JsLearningSeeder"
        "ReactLearningSeeder"
        "PhpLearningSeeder"
        "PythonLearningSeeder"
        "TypeScriptLearningSeeder"
        "TypeScriptPracticeSeeder"
        "AngularPracticeSeeder"
        "AngularLearningSeeder"
        "LaravelLearningSeeder"
        "NodeJsPracticeSeeder"
        "NodeJsLearningSeeder"
        "HtmlPracticeSeeder"
        "CssPracticeSeeder"
        "ExpressPracticeSeeder"
        "ExpressLearningSeeder"
        "MySqlPracticeSeeder"
        "MySqlLearningSeeder"
        "SqlTheoryLearningSeeder"
        "PostgreSqlPracticeSeeder"
        "PostgreSqlLearningSeeder"
    )

    for SEEDER in "${SEEDERS[@]}"; do
        echo "==> Seeding ${SEEDER}..."
        php -d memory_limit=256M artisan db:seed --class="$SEEDER" --force
    done

    echo "==> Seeding complete. Remove FORCE_SEED from env vars now."
else
    echo "==> Seeding (skipped if data exists)..."
    php -d memory_limit=256M artisan db:seed-if-empty
fi

echo "==> App ready."
wait $SERVER_PID
