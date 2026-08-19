export type Language = 'php' | 'javascript';

export interface RunCodePayload {
    language: Language;
    code: string;
}

export interface RunCodeResult {
    output: string;
    exit_code: number;
}
