#!/bin/bash
set -e

echo "==> Starting server on port $PORT..."
php artisan serve --host 0.0.0.0 --port "$PORT" &
SERVER_PID=$!

echo "==> Running migrations..."
php artisan migrate --force

if [ "$FORCE_SEED" = "true" ]; then
    echo "==> FORCE_SEED enabled — seeding each class with retry..."

    seed_with_retry() {
        local SEEDER=$1
        local ATTEMPT=1
        while [ $ATTEMPT -le 3 ]; do
            echo "==> Seeding ${SEEDER} (attempt ${ATTEMPT})..."
            if php -d memory_limit=256M artisan db:seed --class="$SEEDER" --force; then
                return 0
            fi
            echo "==> ${SEEDER} failed. Waiting 15s before retry..."
            sleep 15
            ATTEMPT=$((ATTEMPT + 1))
        done
        echo "FATAL: ${SEEDER} failed after 3 attempts."
        exit 1
    }

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
        seed_with_retry "$SEEDER"
        sleep 5
    done

    echo "==> Seeding complete. Remove FORCE_SEED from env vars now."
else
    echo "==> Seeding (skipped if data exists)..."
    php -d memory_limit=256M artisan db:seed-if-empty
fi

echo "==> App ready."
wait $SERVER_PID
