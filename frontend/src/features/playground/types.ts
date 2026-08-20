export type Language = 'php' | 'javascript' | 'python' | 'mysql';

export interface RunCodePayload {
    language: Language;
    code:     string;
    stdin?:   string;
}

export interface RunCodeResult {
    output:    string;
    exit_code: number;
}

export interface SchemaColumn {
    name:     string;
    type:     string;
    nullable: boolean;
    key:      string;
}

export interface SchemaTable {
    name:    string;
    columns: SchemaColumn[];
}

export interface SchemaResult {
    tables: SchemaTable[];
}

/* ── Battleground types ───────────────────────────── */

export type Difficulty = 'easy' | 'medium' | 'hard';
export type SubmitStatus = 'accepted' | 'wrong_answer' | 'time_limit_exceeded' | 'error' | 'pending';

export interface ProblemListItem {
    id:         number;
    title:      string;
    slug:       string;
    difficulty: Difficulty;
    language:   string;
    status:     SubmitStatus | null;
}

export interface ProblemExample {
    id:              number;
    order:           number;
    label:           string;
    input:           string;
    expected_output: string;
}

export interface ProblemDetail {
    id:               number;
    title:            string;
    slug:             string;
    difficulty:       Difficulty;
    language:         string;
    description:      string;
    constraints:      string | null;
    starter_code:     string | null;
    examples:         ProblemExample[];
    last_submission:  { status: SubmitStatus; test_cases_passed: number; test_cases_total: number } | null;
}

export interface TestCaseResult {
    id:       number;
    order:    number;
    label:    string;
    passed:   boolean;
    hidden:   boolean;
    status:   SubmitStatus;
    input?:   string;
    expected?: string;
    actual?:  string;
}

export interface SubmitResult {
    status:             SubmitStatus;
    test_cases_passed:  number;
    test_cases_total:   number;
    execution_time_ms:  number;
    results:            TestCaseResult[];
}

export interface SubmitPayload {
    language: string;
    code:     string;
}
