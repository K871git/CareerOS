<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class HtmlPracticeSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'frontend-engineering'],
            [
                'title'         => 'Frontend Engineering',
                'description'   => 'Frontend engineering — JavaScript, React, and modern web technologies.',
                'display_order' => 2,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'html'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'HTML',
                'description'       => 'HyperText Markup Language — the backbone of every web page. Master semantic HTML, accessibility, forms, and modern HTML5 APIs.',
                'display_order'     => 4,
            ]
        );

        $levels = [
            [
                'title'         => 'HTML Basics — Junior',
                'slug'          => 'html-junior',
                'description'   => 'Document structure, semantic elements, forms, and core HTML fundamentals. Perfect for junior-level interview preparation.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'HTML Intermediate',
                'slug'          => 'html-intermediate',
                'description'   => 'Accessibility, ARIA, data attributes, media elements, and HTML5 APIs. For developers targeting mid-level roles.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'HTML Advanced',
                'slug'          => 'html-advanced',
                'description'   => 'Web Components, Shadow DOM, performance, and progressive enhancement. Essential for senior developer interviews.',
                'display_order' => 3,
                'difficulty'    => 'Hard',
                'questions'     => $this->advancedQuestions(),
            ],
        ];

        foreach ($levels as $levelData) {
            $topic = Topic::firstOrCreate(
                ['slug' => $levelData['slug']],
                [
                    'subject_id'    => $subject->id,
                    'title'         => $levelData['title'],
                    'description'   => $levelData['description'],
                    'display_order' => $levelData['display_order'],
                ]
            );

            Question::where('topic_id', $topic->id)->delete();

            foreach ($levelData['questions'] as $qData) {
                $question = Question::create([
                    'topic_id'    => $topic->id,
                    'type'        => 'MCQ',
                    'difficulty'  => $levelData['difficulty'],
                    'question'    => $qData['question'],
                    'explanation' => $qData['explanation'],
                ]);

                QuestionOption::insert(array_map(fn ($opt) => [
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ], $qData['options']));
            }
        }

        $this->command->info('HTML Practice seeded: 1 subject, 3 topics, ~100 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────
            [
                'question'    => 'What does HTML stand for?',
                'explanation' => 'HTML stands for HyperText Markup Language. It is the standard markup language for creating web pages. "HyperText" refers to the linking between pages, and "Markup Language" means it uses tags to annotate content, telling the browser how to structure and display it.',
                'options'     => [
                    ['text' => 'HyperText Markup Language', 'correct' => true],
                    ['text' => 'High-Level Text Management Language', 'correct' => false],
                    ['text' => 'HyperText Multiple Language', 'correct' => false],
                    ['text' => 'Home Tool Markup Language', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the correct HTML5 DOCTYPE declaration?',
                'explanation' => '`<!DOCTYPE html>` is the DOCTYPE declaration for HTML5. It must appear at the very beginning of an HTML document, before the `<html>` tag. It tells the browser to render the page in standards mode (not quirks mode). HTML5 simplified the DOCTYPE from the verbose XHTML and HTML 4 versions.',
                'options'     => [
                    ['text' => '<!DOCTYPE html>', 'correct' => true],
                    ['text' => '<!DOCTYPE HTML5>', 'correct' => false],
                    ['text' => '<html doctype="5">', 'correct' => false],
                    ['text' => '<?xml version="1.0"?>', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between block-level and inline elements in HTML?',
                'explanation' => 'Block-level elements (like `<div>`, `<p>`, `<h1>`) start on a new line and stretch to fill the full width of their parent. Inline elements (like `<span>`, `<a>`, `<strong>`) flow within the text line and only take up as much width as their content. CSS can change this behaviour with `display`.',
                'options'     => [
                    ['text' => 'Block elements start on a new line and fill full width; inline elements flow within text', 'correct' => true],
                    ['text' => 'Block elements are only for text; inline elements are for layout', 'correct' => false],
                    ['text' => 'Inline elements cannot contain other elements; block elements can contain any element', 'correct' => false],
                    ['text' => 'Block elements are deprecated in HTML5; inline elements replace them', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is semantic HTML?',
                'explanation' => 'Semantic HTML uses elements that describe the meaning of their content, not just its appearance. Elements like `<header>`, `<nav>`, `<main>`, `<article>`, `<section>`, and `<footer>` clearly communicate the role of their content to browsers, search engines, and screen readers — unlike generic `<div>` elements.',
                'options'     => [
                    ['text' => 'Using HTML elements that describe the meaning and purpose of their content', 'correct' => true],
                    ['text' => 'Writing HTML with proper indentation and formatting', 'correct' => false],
                    ['text' => 'Using CSS classes that describe what an element looks like', 'correct' => false],
                    ['text' => 'A version of HTML that is machine-readable only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `alt` attribute on an `<img>` element?',
                'explanation' => 'The `alt` attribute provides alternative text for an image. It is displayed if the image fails to load, and read aloud by screen readers for users with visual impairments. It also helps search engines understand image content. An empty `alt=""` tells screen readers the image is decorative and can be ignored.',
                'options'     => [
                    ['text' => 'Provides alternative text for accessibility and when the image fails to load', 'correct' => true],
                    ['text' => 'Sets the alignment of the image on the page', 'correct' => false],
                    ['text' => 'Defines an alternate source URL for the image', 'correct' => false],
                    ['text' => 'Specifies the image format to render', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which HTML attribute makes a hyperlink open in a new browser tab?',
                'explanation' => '`target="_blank"` on an `<a>` element opens the link in a new tab or window. For security, it is best practice to also include `rel="noopener noreferrer"` to prevent the new tab from accessing the opener\'s `window.opener` object, which could be exploited for phishing attacks.',
                'options'     => [
                    ['text' => 'target="_blank"', 'correct' => true],
                    ['text' => 'rel="new-tab"', 'correct' => false],
                    ['text' => 'href="_blank"', 'correct' => false],
                    ['text' => 'open="newtab"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<div>` and `<span>`?',
                'explanation' => '`<div>` is a block-level container element — it starts on a new line and takes up the full width. `<span>` is an inline container — it flows within text without breaking the line. Both are non-semantic (generic) elements used for grouping and styling when no more meaningful semantic element fits.',
                'options'     => [
                    ['text' => 'div is block-level (new line, full width); span is inline (flows within text)', 'correct' => true],
                    ['text' => 'div is for JavaScript hooks; span is for CSS styling only', 'correct' => false],
                    ['text' => 'They are identical — just different naming conventions', 'correct' => false],
                    ['text' => 'span can contain block elements; div cannot contain inline elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `<label>` element do in HTML forms?',
                'explanation' => 'A `<label>` associates descriptive text with a form control. When a label is linked via the `for` attribute matching the input\'s `id` (or by wrapping the input), clicking the label focuses the input. This is essential for accessibility — screen readers announce the label when the input is focused.',
                'options'     => [
                    ['text' => 'Associates descriptive text with a form input, improving accessibility', 'correct' => true],
                    ['text' => 'Adds a visible title above a form section', 'correct' => false],
                    ['text' => 'Validates the input and shows an error message', 'correct' => false],
                    ['text' => 'Sets the default placeholder text inside the input', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which HTML element should be used for the most important heading on a page?',
                'explanation' => '`<h1>` is the highest-level heading and should be used for the main title of a page. There should typically be only one `<h1>` per page for good SEO and accessibility. Headings follow a hierarchy: `<h1>` through `<h6>`, from most to least important, helping screen readers and search engines understand page structure.',
                'options'     => [
                    ['text' => '<h1>', 'correct' => true],
                    ['text' => '<header>', 'correct' => false],
                    ['text' => '<title>', 'correct' => false],
                    ['text' => '<heading>', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<meta charset="UTF-8">` tag used for?',
                'explanation' => 'The `<meta charset="UTF-8">` declaration specifies the character encoding for the HTML document. UTF-8 is the standard encoding that supports virtually all characters and symbols from all human languages. It must appear in the `<head>` section to ensure the browser renders text correctly and prevents garbled characters.',
                'options'     => [
                    ['text' => 'Specifies the character encoding so the browser renders text correctly', 'correct' => true],
                    ['text' => 'Sets the page language to English', 'correct' => false],
                    ['text' => 'Defines the viewport width for mobile devices', 'correct' => false],
                    ['text' => 'Links an external CSS framework to the page', 'correct' => false],
                ],
            ],
            // ── Additions (23 more) ───────────────────────────────────────
            [
                'question'    => 'What is the difference between an ordered list (`<ol>`) and an unordered list (`<ul>`)?',
                'explanation' => '`<ol>` renders a numbered (ordered) list — items are displayed with sequential numbers or letters, useful when sequence matters (steps, rankings). `<ul>` renders a bulleted (unordered) list — items are displayed with bullet points, useful when order does not matter. Both use `<li>` for list items.',
                'options'     => [
                    ['text' => '<ol> produces a numbered list; <ul> produces a bulleted list', 'correct' => true],
                    ['text' => '<ol> is for navigation menus; <ul> is for content lists', 'correct' => false],
                    ['text' => 'They are identical — the browser renders them the same way', 'correct' => false],
                    ['text' => '<ol> requires CSS to show numbers; <ul> shows bullets by default only in some browsers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which HTML elements make up the basic structure of a table?',
                'explanation' => 'An HTML table uses `<table>` as the wrapper. `<thead>` groups header rows, `<tbody>` groups body rows, and `<tfoot>` groups footer rows. Inside, `<tr>` defines a table row, `<th>` defines a header cell (bold and centred by default), and `<td>` defines a data cell. Using `<thead>` and `<tbody>` helps accessibility tools and enables scrollable table bodies.',
                'options'     => [
                    ['text' => 'table, thead, tbody, tr, th, td', 'correct' => true],
                    ['text' => 'table, row, col, header, cell', 'correct' => false],
                    ['text' => 'grid, tr, td, th', 'correct' => false],
                    ['text' => 'table, section, tr, data', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between the `href` attribute and the `src` attribute in HTML?',
                'explanation' => '`href` (hypertext reference) specifies a link destination — it is used on `<a>` and `<link>` elements to navigate to or reference a resource. `src` (source) specifies the URL of embedded content that is fetched and displayed in place — used on `<img>`, `<script>`, `<iframe>`, and `<video>`. `href` links; `src` embeds.',
                'options'     => [
                    ['text' => 'href links to a resource; src embeds/fetches a resource directly into the element', 'correct' => true],
                    ['text' => 'They are interchangeable on all elements', 'correct' => false],
                    ['text' => 'src is for external resources; href is for local resources only', 'correct' => false],
                    ['text' => 'href is used only on images; src is used only on links', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `<form>` element in HTML?',
                'explanation' => 'The `<form>` element creates an interactive section for collecting user input. It wraps form controls like `<input>`, `<select>`, and `<textarea>`. Key attributes include `action` (URL to submit data to), `method` (GET or POST), and `enctype` (encoding type). Submitting the form sends the collected data to the server.',
                'options'     => [
                    ['text' => 'Wraps form controls to collect and submit user input to a server', 'correct' => true],
                    ['text' => 'Creates a styled card layout for displaying content', 'correct' => false],
                    ['text' => 'Validates input fields and displays errors automatically', 'correct' => false],
                    ['text' => 'Defines the visual structure of a data entry table', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which `<input>` type should be used to collect an email address from a user?',
                'explanation' => '`<input type="email">` tells the browser the field expects an email address. On mobile it shows an email-optimised keyboard, and the browser performs basic validation (must contain "@" and a domain) before form submission. Other useful input types include `text`, `password`, `number`, `checkbox`, and `radio`.',
                'options'     => [
                    ['text' => '<input type="email">', 'correct' => true],
                    ['text' => '<input type="text" format="email">', 'correct' => false],
                    ['text' => '<input type="mail">', 'correct' => false],
                    ['text' => '<input type="user-email">', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<input type="submit">` and `<button type="button">` inside a form?',
                'explanation' => '`<input type="submit">` and `<button type="submit">` both submit the form when clicked. `<button type="button">` does NOT submit the form — it is a plain button with no default action, intended for JavaScript event handlers. `<button type="reset">` clears all form fields. Knowing the default type matters: a `<button>` inside a form defaults to `type="submit"` if the attribute is omitted.',
                'options'     => [
                    ['text' => 'type="submit" submits the form; type="button" does nothing by default (used with JS)', 'correct' => true],
                    ['text' => 'They are identical — both submit the form', 'correct' => false],
                    ['text' => 'type="button" submits the form; type="submit" only validates it', 'correct' => false],
                    ['text' => 'type="submit" works only with GET method; type="button" works with POST', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `required` attribute do on a form input?',
                'explanation' => 'The `required` attribute is a boolean attribute that prevents the form from being submitted if the field is empty (or unchecked for checkboxes). The browser shows a native validation message. It is part of the HTML5 Constraint Validation API, which also includes `min`, `max`, `pattern`, and `maxlength`.',
                'options'     => [
                    ['text' => 'Prevents form submission if the field is empty, with a native browser validation message', 'correct' => true],
                    ['text' => 'Makes the field read-only so users cannot change the value', 'correct' => false],
                    ['text' => 'Highlights the field in red until a value is entered', 'correct' => false],
                    ['text' => 'Sends the field value to the server even if it is empty', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `placeholder` attribute on an `<input>` element?',
                'explanation' => 'The `placeholder` attribute displays hint text inside the input field when it is empty, giving users an example of the expected format (e.g., "Enter your email"). It disappears as soon as the user begins typing. It is NOT a substitute for a `<label>` — labels persist and are essential for accessibility.',
                'options'     => [
                    ['text' => 'Shows hint text inside the field when it is empty; disappears when the user types', 'correct' => true],
                    ['text' => 'Sets the default value that is submitted if the user leaves the field empty', 'correct' => false],
                    ['text' => 'Replaces the label element for accessibility purposes', 'correct' => false],
                    ['text' => 'Defines the maximum number of characters allowed in the field', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Why is the `name` attribute important on form inputs?',
                'explanation' => 'The `name` attribute is the key used when form data is sent to a server. When the form submits, the browser creates key-value pairs using `name=value` for each field. Without a `name`, the field\'s value is excluded from the submission. For radio button groups, inputs that share the same `name` are treated as a single selection group.',
                'options'     => [
                    ['text' => 'It is the key used in the submitted form data (name=value pairs sent to the server)', 'correct' => true],
                    ['text' => 'It sets the visible label displayed above the input', 'correct' => false],
                    ['text' => 'It is used only for CSS targeting — it has no effect on form submission', 'correct' => false],
                    ['text' => 'It links the input to a database column name on the server', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you write a comment in HTML?',
                'explanation' => 'HTML comments start with `<!--` and end with `-->`. Everything between these markers is ignored by the browser and not rendered on the page. Comments are useful for documenting code, temporarily disabling markup, and leaving notes for other developers. They are still visible in the page source, so sensitive information should never be put in comments.',
                'options'     => [
                    ['text' => '<!-- comment text -->', 'correct' => true],
                    ['text' => '// comment text', 'correct' => false],
                    ['text' => '/* comment text */', 'correct' => false],
                    ['text' => '# comment text', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `<head>` section in an HTML document?',
                'explanation' => 'The `<head>` section contains metadata about the document — information that is not displayed directly on the page. This includes the `<title>` (browser tab text), character encoding (`<meta charset>`), viewport settings, linked stylesheets (`<link>`), scripts, SEO meta tags (description, robots), and Open Graph tags. The `<head>` is read by browsers and search engines but not shown to users.',
                'options'     => [
                    ['text' => 'Contains metadata about the document (title, character set, stylesheets, scripts) not shown on the page', 'correct' => true],
                    ['text' => 'Holds the top navigation bar and site logo', 'correct' => false],
                    ['text' => 'Defines the header section displayed at the top of every page', 'correct' => false],
                    ['text' => 'Wraps the first paragraph of content on a page', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `<meta name="description">` tag do?',
                'explanation' => 'The `<meta name="description" content="...">` tag provides a short summary of the page\'s content. Search engines like Google may use this text as the snippet shown below a page title in search results. It does not directly affect ranking but can improve click-through rates. It should be unique per page and around 150-160 characters.',
                'options'     => [
                    ['text' => 'Provides a page summary that search engines may display as the result snippet', 'correct' => true],
                    ['text' => 'Sets the browser tab title for the page', 'correct' => false],
                    ['text' => 'Prevents the page from being indexed by search engines', 'correct' => false],
                    ['text' => 'Adds a tooltip that appears when the user hovers over the browser tab', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you link an external CSS stylesheet to an HTML page?',
                'explanation' => 'You link an external CSS file using the `<link>` element inside `<head>`: `<link rel="stylesheet" href="styles.css">`. The `rel="stylesheet"` attribute tells the browser the linked file is a CSS stylesheet. This keeps HTML structure and CSS presentation separated, which is a core best practice.',
                'options'     => [
                    ['text' => '<link rel="stylesheet" href="styles.css"> inside the <head>', 'correct' => true],
                    ['text' => '<css src="styles.css"> inside the <body>', 'correct' => false],
                    ['text' => '<script type="text/css" src="styles.css">', 'correct' => false],
                    ['text' => '<style href="styles.css"> inside the <head>', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Why is it recommended to place `<script>` tags at the end of the `<body>` rather than in `<head>`?',
                'explanation' => 'Browsers parse HTML top to bottom. A `<script>` in `<head>` without `defer` or `async` blocks parsing until the script is downloaded and executed, delaying page render. Placing scripts at the end of `<body>` means HTML is fully parsed and visible first. Modern best practice uses `defer` in `<head>` instead, which achieves the same result without moving the tag.',
                'options'     => [
                    ['text' => 'Scripts in <head> block HTML parsing; placing them at the end lets the page render first', 'correct' => true],
                    ['text' => 'Scripts only work when placed inside <body>', 'correct' => false],
                    ['text' => 'Scripts in <head> are executed twice — once on load and once on DOMContentLoaded', 'correct' => false],
                    ['text' => 'Browsers ignore scripts in <head> for security reasons', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between the `<br>` and `<p>` elements?',
                'explanation' => '`<br>` is a void element that inserts a single line break within inline content, useful inside a poem or address where line breaks are meaningful. `<p>` is a block-level element that wraps a paragraph of text and adds vertical spacing (margin) above and below. Do not use multiple `<br>` tags to create spacing between paragraphs — use `<p>` or CSS margins instead.',
                'options'     => [
                    ['text' => '<br> inserts a line break; <p> wraps a full paragraph with vertical spacing', 'correct' => true],
                    ['text' => 'They are the same — both create a new line', 'correct' => false],
                    ['text' => '<p> is deprecated; <br> is the modern replacement for paragraphs', 'correct' => false],
                    ['text' => '<br> adds bottom margin; <p> only adds top margin', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<strong>` and `<em>` in HTML?',
                'explanation' => '`<strong>` indicates strong importance — it renders as bold by default and signals to screen readers that the content is critical. `<em>` indicates emphasis — it renders as italic by default and conveys spoken stress (like italics in a book). Both are semantic elements with meaning, unlike the purely visual `<b>` and `<i>` elements.',
                'options'     => [
                    ['text' => '<strong> marks important text (bold); <em> marks emphasised text (italic)', 'correct' => true],
                    ['text' => 'They are identical — both just change the font weight', 'correct' => false],
                    ['text' => '<em> is for headings; <strong> is for body text', 'correct' => false],
                    ['text' => '<strong> is deprecated; <em> replaced it in HTML5', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do the `width` and `height` attributes on an `<img>` element do?',
                'explanation' => 'The `width` and `height` attributes specify the dimensions of the image in CSS pixels. Setting both attributes lets the browser reserve the correct space in the layout before the image loads, preventing layout shift (CLS — Cumulative Layout Shift). This is important for Core Web Vitals. If you omit them, the page reflows when the image loads.',
                'options'     => [
                    ['text' => 'Reserve space in the layout before the image loads, preventing layout shift', 'correct' => true],
                    ['text' => 'Force the image to stretch to exactly those pixel dimensions regardless of aspect ratio', 'correct' => false],
                    ['text' => 'They are cosmetic only — they have no effect on layout or performance', 'correct' => false],
                    ['text' => 'They limit the file size that can be loaded from the src URL', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the `<figure>` and `<figcaption>` elements used for?',
                'explanation' => '`<figure>` wraps self-contained content that is referenced from the main flow — typically images, code snippets, or diagrams. `<figcaption>` provides a caption for the figure. This semantic grouping tells browsers and screen readers that the caption describes the figure. It is the correct way to associate a caption with an image rather than placing text near it without a structural relationship.',
                'options'     => [
                    ['text' => '<figure> wraps self-contained media; <figcaption> provides its accessible caption', 'correct' => true],
                    ['text' => '<figure> is a deprecated name for <img>; <figcaption> replaces the alt attribute', 'correct' => false],
                    ['text' => 'They create a bordered box with a title, similar to a CSS card component', 'correct' => false],
                    ['text' => '<figure> groups multiple images; <figcaption> labels only the first image', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which attributes are required on the `<video>` element for basic cross-browser playback?',
                'explanation' => 'The `<video>` element embeds video. The `src` attribute (or nested `<source>` elements) specifies the video file. `controls` adds the browser\'s native play/pause/volume UI. `width` and `height` are recommended to prevent layout shift. Common optional attributes include `autoplay`, `muted`, `loop`, and `poster`. For broad support, provide both MP4 (H.264) and WebM sources.',
                'options'     => [
                    ['text' => 'src (or nested <source>) and controls for the native playback UI', 'correct' => true],
                    ['text' => 'type and codec are the only required attributes', 'correct' => false],
                    ['text' => 'autoplay and muted are required or the video will not play', 'correct' => false],
                    ['text' => 'The <video> element cannot function without a JavaScript media player', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you embed audio in HTML5?',
                'explanation' => 'The `<audio>` element embeds audio. Use `src` to point to the audio file, or nest `<source>` elements for multiple format fallbacks (MP3, OGG). The `controls` attribute renders the browser\'s native audio player. Optional attributes include `autoplay`, `loop`, and `muted`. Always provide fallback text inside `<audio>` for browsers that do not support it.',
                'options'     => [
                    ['text' => '<audio src="file.mp3" controls></audio>', 'correct' => true],
                    ['text' => '<sound src="file.mp3">', 'correct' => false],
                    ['text' => '<media type="audio" src="file.mp3">', 'correct' => false],
                    ['text' => '<embed type="audio/mp3" src="file.mp3">', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an `<iframe>` element and what is a key security concern with it?',
                'explanation' => '`<iframe>` (inline frame) embeds another HTML document or web page inside the current page. A key security concern is clickjacking — an attacker can embed your site in an iframe on a malicious site and trick users into clicking hidden buttons. Mitigation: set the `X-Frame-Options: DENY` or `SAMEORIGIN` HTTP header, or use `Content-Security-Policy: frame-ancestors`. The `sandbox` attribute on the iframe itself also restricts embedded content capabilities.',
                'options'     => [
                    ['text' => 'Embeds another page inside the current page; clickjacking is the key security concern', 'correct' => true],
                    ['text' => 'Creates a floating overlay panel; the security concern is CORS blocking', 'correct' => false],
                    ['text' => 'Embeds JavaScript directly in HTML; the security concern is XSS', 'correct' => false],
                    ['text' => 'Creates an inline form; the security concern is CSRF', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an invalid HTML nesting rule that browsers silently correct?',
                'explanation' => 'HTML has content model rules about which elements can contain others. A block-level element like `<p>` or `<div>` cannot be placed inside an inline element like `<a>` or `<span>`. While browsers usually auto-correct invalid nesting, the resulting DOM may not be what the developer intended, leading to unexpected styling and layout bugs. Always follow nesting rules explicitly.',
                'options'     => [
                    ['text' => 'Placing a block element (e.g., <div>) inside an inline element (e.g., <span>) is invalid', 'correct' => true],
                    ['text' => 'Placing <li> inside <ul> is invalid — <li> must be a direct child of <ol> only', 'correct' => false],
                    ['text' => 'Placing <a> inside <p> is invalid in HTML5', 'correct' => false],
                    ['text' => 'Placing <img> inside <figure> is invalid and must use <picture> instead', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a void element in HTML?',
                'explanation' => 'Void elements are HTML elements that cannot have any child content and therefore have no closing tag. Examples include `<br>`, `<hr>`, `<img>`, `<input>`, `<link>`, `<meta>`, `<area>`, `<base>`, `<col>`, `<embed>`, `<param>`, `<source>`, `<track>`, and `<wbr>`. In HTML5 the self-closing slash is optional (`<br>` and `<br />` are both valid).',
                'options'     => [
                    ['text' => 'Elements that cannot have children and do not need a closing tag (e.g., <br>, <img>, <input>)', 'correct' => true],
                    ['text' => 'Elements that are empty by default but can accept child elements', 'correct' => false],
                    ['text' => 'Deprecated elements removed from the HTML5 specification', 'correct' => false],
                    ['text' => 'Elements that are invisible but still take up space in the layout', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────
            [
                'question'    => 'What are HTML data attributes (data-*)?',
                'explanation' => 'Data attributes (`data-*`) allow you to store custom data on HTML elements: `<div data-user-id="42">`. They can be accessed in JavaScript via `element.dataset.userId`. They are useful for attaching metadata to elements without using non-standard attributes or storing data in the DOM, and they do not affect styling or behaviour unless targeted by JavaScript/CSS.',
                'options'     => [
                    ['text' => 'Custom attributes for storing private data on elements, accessible via element.dataset', 'correct' => true],
                    ['text' => 'Attributes that enable server-side data binding to elements', 'correct' => false],
                    ['text' => 'Built-in HTML attributes prefixed with "data" for form validation', 'correct' => false],
                    ['text' => 'Attributes that define data types for input fields', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of ARIA roles and attributes in HTML?',
                'explanation' => 'ARIA (Accessible Rich Internet Applications) roles and attributes provide additional semantic information to assistive technologies like screen readers. For example, `role="button"` makes a `<div>` behave semantically as a button, and `aria-label="Close"` provides a text description. ARIA should be used when native HTML semantics are insufficient.',
                'options'     => [
                    ['text' => 'Improve accessibility by providing semantic information to assistive technologies', 'correct' => true],
                    ['text' => 'Define animation roles for CSS transitions on elements', 'correct' => false],
                    ['text' => 'Enable real-time data binding between HTML and JavaScript', 'correct' => false],
                    ['text' => 'Set permissions for which scripts can access an element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `<meta name="viewport" content="width=device-width, initial-scale=1">` do?',
                'explanation' => 'This meta tag controls how the browser scales pages on mobile devices. Without it, mobile browsers render pages at a desktop width and zoom out. `width=device-width` sets the viewport width to the device\'s actual width, and `initial-scale=1` sets the initial zoom level to 100%. It is essential for responsive design.',
                'options'     => [
                    ['text' => 'Controls viewport scaling on mobile devices for responsive design', 'correct' => true],
                    ['text' => 'Sets the background colour of the browser chrome on mobile', 'correct' => false],
                    ['text' => 'Declares the page as a Progressive Web App', 'correct' => false],
                    ['text' => 'Prevents the browser from caching the page', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<strong>` and `<b>` in HTML?',
                'explanation' => '`<strong>` has semantic meaning — it indicates that the enclosed text is of strong importance, seriousness, or urgency. Screen readers may emphasize it differently. `<b>` is purely presentational — it renders text in bold without conveying any meaning. Modern best practice favours `<strong>` over `<b>` for meaningful content.',
                'options'     => [
                    ['text' => '<strong> conveys importance (semantic); <b> is just visual bold (no meaning)', 'correct' => true],
                    ['text' => 'They are identical — both just make text bold', 'correct' => false],
                    ['text' => '<b> is the newer HTML5 element; <strong> is deprecated', 'correct' => false],
                    ['text' => '<strong> only works inside <p> elements; <b> works anywhere', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `loading="lazy"` attribute on images?',
                'explanation' => '`loading="lazy"` defers loading of an image until it is close to entering the viewport (the visible area). This improves initial page load performance by not fetching off-screen images immediately. The browser loads them as the user scrolls. It is natively supported in modern browsers with no JavaScript required.',
                'options'     => [
                    ['text' => 'Defers image loading until the image is near the viewport, improving page load time', 'correct' => true],
                    ['text' => 'Loads a low-quality placeholder first, then the full image', 'correct' => false],
                    ['text' => 'Prevents the image from loading on slow connections', 'correct' => false],
                    ['text' => 'Makes the image load asynchronously using JavaScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are HTML entities and when do you need them?',
                'explanation' => 'HTML entities are special codes representing characters that have special meaning in HTML or are not easily typed. For example, `&lt;` renders as `<`, `&gt;` as `>`, `&amp;` as `&`, and `&nbsp;` as a non-breaking space. They are needed to display reserved characters without the browser interpreting them as HTML markup.',
                'options'     => [
                    ['text' => 'Special codes to display reserved characters like < > & as literal text', 'correct' => true],
                    ['text' => 'JavaScript objects built into HTML for DOM manipulation', 'correct' => false],
                    ['text' => 'Custom HTML elements defined by web component specs', 'correct' => false],
                    ['text' => 'Attributes that link to external resources like images or scripts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `<details>` and `<summary>` HTML5 element pair do?',
                'explanation' => '`<details>` creates a disclosure widget — a collapsible section that can be opened and closed by the user. `<summary>` provides the visible heading/toggle. Clicking the summary toggles the details section open or closed. No JavaScript is required — it is a native browser behaviour. Useful for FAQs, additional information sections, or collapsible menus.',
                'options'     => [
                    ['text' => 'Creates a native collapsible/disclosure widget without JavaScript', 'correct' => true],
                    ['text' => 'Displays a database record and a summary in a table format', 'correct' => false],
                    ['text' => 'Renders a modal dialog with a title and body', 'correct' => false],
                    ['text' => 'Creates an accordion component using built-in CSS animation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between the `defer` and `async` attributes on a `<script>` tag?',
                'explanation' => 'Both `defer` and `async` load scripts without blocking HTML parsing. `defer` executes scripts in order after the HTML is fully parsed. `async` executes scripts as soon as they download, in any order, potentially interrupting parsing. Use `defer` for scripts that depend on each other or the DOM; use `async` for independent scripts like analytics.',
                'options'     => [
                    ['text' => 'defer executes after parsing in order; async executes immediately when downloaded in any order', 'correct' => true],
                    ['text' => 'async blocks parsing; defer does not', 'correct' => false],
                    ['text' => 'They are identical — both execute after the page fully loads', 'correct' => false],
                    ['text' => 'defer is for external scripts only; async works for inline scripts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<picture>` element used for in HTML5?',
                'explanation' => '`<picture>` provides multiple `<source>` elements with different image files based on conditions (media queries, image format support). The browser picks the most appropriate source. This enables responsive images (different sizes for different viewports) and format fallbacks (WebP for modern browsers, JPEG for older ones). A `<img>` inside is the required fallback.',
                'options'     => [
                    ['text' => 'Serving different images based on viewport size or format support (responsive images)', 'correct' => true],
                    ['text' => 'Embedding a picture frame with title and caption', 'correct' => false],
                    ['text' => 'Creating an image gallery with built-in navigation', 'correct' => false],
                    ['text' => 'Displaying images in a floating picture-in-picture overlay', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<template>` element in HTML?',
                'explanation' => 'The `<template>` element holds client-side HTML markup that is not rendered when the page loads. Its content is inert — scripts inside do not run, images do not load. The content can be cloned and inserted into the document with JavaScript: `document.importNode(template.content, true)`. It is the basis for Web Components and efficient DOM creation.',
                'options'     => [
                    ['text' => 'Holds HTML that is not rendered until cloned and inserted via JavaScript', 'correct' => true],
                    ['text' => 'A server-side template tag for injecting dynamic content', 'correct' => false],
                    ['text' => 'Defines a reusable CSS template for multiple elements', 'correct' => false],
                    ['text' => 'An older name for the <html> root element', 'correct' => false],
                ],
            ],
            // ── Additions (23 more) ───────────────────────────────────────
            [
                'question'    => 'What are `<fieldset>` and `<legend>` used for in HTML forms?',
                'explanation' => '`<fieldset>` groups related form controls together with a visual border. `<legend>` provides a caption for the fieldset, displayed at the top of the border. This semantic grouping helps screen readers announce the group context when a user focuses on a control inside it (e.g., "Shipping address — First name"). It is essential for accessible, complex forms.',
                'options'     => [
                    ['text' => '<fieldset> groups related controls; <legend> provides the group\'s accessible caption', 'correct' => true],
                    ['text' => '<fieldset> validates all inputs inside it; <legend> sets the error message', 'correct' => false],
                    ['text' => 'They are cosmetic wrappers with no accessibility significance', 'correct' => false],
                    ['text' => '<legend> is the label for the entire form; <fieldset> is a section separator', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do `<select>` and `<option>` work together in HTML?',
                'explanation' => '`<select>` creates a dropdown (combo box) form control. Each choice is an `<option>` element nested inside. The `value` attribute of `<option>` is the data sent on form submission; the element\'s text content is what the user sees. `<optgroup>` can group related options with a label. The `multiple` attribute on `<select>` allows multi-selection.',
                'options'     => [
                    ['text' => '<select> creates the dropdown; <option> elements define the individual choices', 'correct' => true],
                    ['text' => '<select> is a text input; <option> adds autocomplete suggestions', 'correct' => false],
                    ['text' => 'They create a radio button group — only one option can be active', 'correct' => false],
                    ['text' => '<select> is deprecated; use <datalist> with <input> instead in all cases', 'correct' => false],
                ],
            ],
            [
                'question'    => 'When should you use `<textarea>` instead of `<input type="text">`?',
                'explanation' => '`<textarea>` is for multi-line text input (e.g., comments, messages, addresses). It can be resized by the user and supports newline characters. `<input type="text">` is a single-line field. `<textarea>` has `rows` and `cols` attributes for default size, and its default value is placed between the opening and closing tags (not in a `value` attribute).',
                'options'     => [
                    ['text' => 'When you need multi-line text input; textarea supports newlines and is resizable', 'correct' => true],
                    ['text' => 'When you need to accept file uploads from the user', 'correct' => false],
                    ['text' => 'When the input should be styled with a custom font', 'correct' => false],
                    ['text' => 'textarea is only used when input type="text" fails validation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `pattern` attribute on an `<input>` element do?',
                'explanation' => 'The `pattern` attribute specifies a regular expression the input\'s value must match for the form to submit. For example, `pattern="[A-Za-z]{3}"` requires exactly three letters. The browser shows a native validation message if the pattern is not satisfied. Always include a `title` attribute describing the required format, as browsers display it in the error tooltip.',
                'options'     => [
                    ['text' => 'Specifies a regex the input value must match for native browser validation', 'correct' => true],
                    ['text' => 'Applies a CSS background pattern to the input field', 'correct' => false],
                    ['text' => 'Defines the input mask (e.g., phone number format) displayed as placeholder text', 'correct' => false],
                    ['text' => 'Links the input to a server-side validation pattern by name', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `autocomplete` attribute control on a form or input?',
                'explanation' => '`autocomplete="on"` (the default) allows the browser to suggest previously entered values. `autocomplete="off"` disables browser autofill for the field. Specific values like `autocomplete="email"`, `autocomplete="new-password"`, or `autocomplete="current-password"` hint to password managers and browsers what kind of data the field expects, enabling smart autofill and preventing misfills.',
                'options'     => [
                    ['text' => 'Controls whether the browser can autofill the field with previously entered values', 'correct' => true],
                    ['text' => 'Enables a custom JavaScript autocomplete dropdown on the input', 'correct' => false],
                    ['text' => 'Automatically submits the form when the user stops typing', 'correct' => false],
                    ['text' => 'Completes the HTML attribute names automatically in the browser DevTools', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `novalidate` attribute on a `<form>` element do?',
                'explanation' => '`novalidate` is a boolean attribute that disables the browser\'s built-in HTML5 Constraint Validation — form submission proceeds even if fields fail `required`, `pattern`, `type`, `min`, or `max` constraints. It is commonly used when a developer implements custom JavaScript validation and does not want the browser\'s native messages interfering.',
                'options'     => [
                    ['text' => 'Disables all native browser HTML5 validation so the form always submits', 'correct' => true],
                    ['text' => 'Prevents the form from being submitted at all', 'correct' => false],
                    ['text' => 'Removes validation only from required fields, keeping type checks', 'correct' => false],
                    ['text' => 'Turns off server-side validation for the form\'s action endpoint', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `method="GET"` and `method="POST"` on an HTML form?',
                'explanation' => 'GET appends form data to the URL as a query string (e.g., `?name=Alice&age=30`). It is idempotent, bookmarkable, and cached — suitable for searches. POST sends data in the HTTP request body, not the URL — suitable for sensitive data (passwords), large payloads, and state-changing operations (creating/updating records). File uploads require POST with `enctype="multipart/form-data"`.',
                'options'     => [
                    ['text' => 'GET appends data to the URL (visible, cacheable); POST sends data in the request body (hidden, not cached)', 'correct' => true],
                    ['text' => 'GET is for reading data only; POST is exclusively for updating records', 'correct' => false],
                    ['text' => 'They are interchangeable — the server decides which to use', 'correct' => false],
                    ['text' => 'POST encrypts the form data; GET sends it unencrypted', 'correct' => false],
                ],
            ],
            [
                'question'    => 'When is `enctype="multipart/form-data"` required on a form?',
                'explanation' => '`enctype="multipart/form-data"` is required when the form includes a file upload input (`<input type="file">`). It changes how the browser encodes the form data — instead of URL-encoding, it splits the body into separate parts (one per field), each with its own headers. Without it, only the filename (not the file content) is sent. The form must also use `method="POST"`.',
                'options'     => [
                    ['text' => 'When the form contains a file upload input (<input type="file">)', 'correct' => true],
                    ['text' => 'When the form data contains special characters or spaces', 'correct' => false],
                    ['text' => 'When the form uses the GET method', 'correct' => false],
                    ['text' => 'When the form submits to an HTTPS endpoint', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `tabindex` attribute control in HTML?',
                'explanation' => '`tabindex` controls the order in which elements receive keyboard focus when the user presses Tab. `tabindex="0"` adds the element to the natural tab order (based on DOM position). A positive integer (e.g., `tabindex="2"`) sets explicit order (generally discouraged as it disrupts natural flow). `tabindex="-1"` removes the element from the tab order but still allows programmatic focus via JavaScript (`element.focus()`).',
                'options'     => [
                    ['text' => 'Controls keyboard focus order; -1 removes from tab order but allows programmatic focus', 'correct' => true],
                    ['text' => 'Sets the z-index of an element for overlapping layout', 'correct' => false],
                    ['text' => 'Specifies the number of spaces a tab character creates inside the element', 'correct' => false],
                    ['text' => 'Defines which tab in a tabbed interface the element belongs to', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `accesskey` attribute do?',
                'explanation' => '`accesskey` defines a keyboard shortcut for activating or focusing an element. For example, `<a href="/home" accesskey="h">Home</a>` — pressing the browser\'s accesskey modifier (Alt on Windows, Ctrl+Option on Mac) plus "h" activates the link. While useful for power users and accessibility, conflicts with browser and OS shortcuts and inconsistent behaviour across browsers make it difficult to use in practice.',
                'options'     => [
                    ['text' => 'Defines a keyboard shortcut (modifier + key) to activate or focus the element', 'correct' => true],
                    ['text' => 'Sets an API access key for authenticated requests from the element', 'correct' => false],
                    ['text' => 'Restricts access to the element for users without the specified key role', 'correct' => false],
                    ['text' => 'Stores a secret key value in the element for JavaScript retrieval', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `<input type="hidden">` used for?',
                'explanation' => '`<input type="hidden">` stores a value that is submitted with the form but is not visible or editable by the user. Common uses include CSRF tokens, record IDs, and state values the server needs but the user should not change. The value is still visible in the page source, so it is not a security measure — do not store sensitive data in hidden inputs without server-side validation.',
                'options'     => [
                    ['text' => 'Submits a value with the form that is invisible and non-editable to the user', 'correct' => true],
                    ['text' => 'Creates a password field that hides characters as the user types', 'correct' => false],
                    ['text' => 'Hides the entire form from the user until a condition is met', 'correct' => false],
                    ['text' => 'Stores data securely in encrypted form before submission', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<datalist>` element and how does it differ from `<select>`?',
                'explanation' => '`<datalist>` provides a list of predefined options for an `<input>` element via the `list` attribute. Unlike `<select>`, the user can still type any value — the datalist options are suggestions, not restrictions. This combines the freedom of a text input with the convenience of an autocomplete dropdown. Example: `<input list="browsers"><datalist id="browsers"><option value="Chrome"></datalist>`.',
                'options'     => [
                    ['text' => '<datalist> provides autocomplete suggestions for a text input; the user can still type anything', 'correct' => true],
                    ['text' => '<datalist> is identical to <select> but with a different visual style', 'correct' => false],
                    ['text' => '<datalist> fetches suggestions from a server API automatically', 'correct' => false],
                    ['text' => '<datalist> restricts the input to only the listed values, like <select>', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `<output>` element represent in HTML?',
                'explanation' => '`<output>` is a semantic element that represents the result of a calculation or user action. It is often used with form inputs to display computed values. The `for` attribute links it to the input elements whose values contributed to the result (space-separated list of IDs). Example: a range slider that updates a `<output>` with the current value via JavaScript.',
                'options'     => [
                    ['text' => 'Represents the result of a calculation, typically updated dynamically via JavaScript', 'correct' => true],
                    ['text' => 'Defines the server\'s output response displayed inside the form', 'correct' => false],
                    ['text' => 'An alias for <span> used specifically inside forms', 'correct' => false],
                    ['text' => 'A read-only input that shows a value from the database', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<progress>` element used for in HTML5?',
                'explanation' => 'The `<progress>` element represents the completion progress of a task. The `value` attribute sets the current value and `max` sets the maximum. `<progress value="70" max="100">` renders a progress bar at 70%. If `value` is omitted, the progress is indeterminate (animated without a defined percentage). It has built-in accessibility — assistive technologies announce it as a progress indicator.',
                'options'     => [
                    ['text' => 'Displays a progress bar showing task completion; omit value for indeterminate state', 'correct' => true],
                    ['text' => 'Shows a page loading animation until all resources are fetched', 'correct' => false],
                    ['text' => 'Tracks form completion percentage based on filled fields', 'correct' => false],
                    ['text' => 'Wraps a section of content that loads progressively', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<meter>` element and how does it differ from `<progress>`?',
                'explanation' => '`<meter>` represents a scalar measurement within a known range — like disk usage, a quiz score, or a rating. Unlike `<progress>` (which shows task completion), `<meter>` shows a value within a fixed range with optional `low`, `high`, and `optimum` attributes that can change the bar colour. Example: `<meter value="0.6" min="0" max="1" low="0.25" high="0.75" optimum="1">60%</meter>`.',
                'options'     => [
                    ['text' => '<meter> shows a scalar value within a known range; <progress> shows task completion', 'correct' => true],
                    ['text' => 'They are identical; meter is just a more semantic name for progress', 'correct' => false],
                    ['text' => '<meter> is for temperature; <progress> is for percentages', 'correct' => false],
                    ['text' => '<meter> requires JavaScript to update; <progress> updates automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are `<map>` and `<area>` elements in HTML?',
                'explanation' => '`<map>` defines an image map — an image with clickable regions. `<area>` elements inside define the clickable hotspots with `shape` (rect, circle, poly), `coords`, `href`, and `alt` attributes. The `<img>` element links to the map via its `usemap="#mapname"` attribute. Image maps are mostly a legacy feature; CSS/SVG solutions are preferred for modern interactive diagrams.',
                'options'     => [
                    ['text' => '<map> defines an image map; <area> elements specify clickable hotspot regions on the image', 'correct' => true],
                    ['text' => '<map> embeds a Google Maps iframe; <area> pins locations on it', 'correct' => false],
                    ['text' => '<map> creates a site navigation map; <area> defines each section', 'correct' => false],
                    ['text' => '<map> and <area> define geographic data for SVG charts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<object>` and `<embed>` elements?',
                'explanation' => '`<object>` embeds external content (PDFs, Flash, applets) and supports fallback content between its tags. `<embed>` is a void element for embedding external content without fallback. Both are largely legacy — most use cases are now handled by `<video>`, `<audio>`, `<img>`, or JavaScript. `<object>` is preferred over `<embed>` when fallback content is needed.',
                'options'     => [
                    ['text' => '<object> embeds external content with fallback children; <embed> is a void element with no fallback', 'correct' => true],
                    ['text' => '<embed> is for videos; <object> is for documents', 'correct' => false],
                    ['text' => 'They are identical — modern browsers treat them the same', 'correct' => false],
                    ['text' => '<object> requires a plugin; <embed> uses native browser APIs only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do `srcset` and `sizes` attributes on an `<img>` element do?',
                'explanation' => '`srcset` provides a list of image URLs with their widths (e.g., `image-480w.jpg 480w, image-800w.jpg 800w`). `sizes` tells the browser how wide the image will be at different viewport widths (e.g., `(max-width: 600px) 480px, 800px`). The browser uses both to select the optimal image to download — reducing bandwidth by not loading oversized images on small screens.',
                'options'     => [
                    ['text' => 'srcset lists candidate images with widths; sizes tells the browser the image\'s display width at each viewport', 'correct' => true],
                    ['text' => 'srcset defines animation frames; sizes sets the image dimensions in CSS', 'correct' => false],
                    ['text' => 'They are used only inside <picture> and have no effect on <img>', 'correct' => false],
                    ['text' => 'srcset sets the default image; sizes provides fallback images', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `<link rel="preload">` do?',
                'explanation' => '`<link rel="preload" href="font.woff2" as="font">` instructs the browser to fetch a resource early — before it is discovered in the normal parse flow — without blocking rendering. The `as` attribute tells the browser the resource type (font, style, script, image) so it applies correct priority and Content Security Policy. It is used for critical resources like fonts, hero images, and key scripts.',
                'options'     => [
                    ['text' => 'Fetches a critical resource early at high priority without blocking rendering', 'correct' => true],
                    ['text' => 'Loads the resource only after the page has fully rendered', 'correct' => false],
                    ['text' => 'Preloads an entire page into the cache for instant navigation', 'correct' => false],
                    ['text' => 'It is an alias for <link rel="prefetch"> with higher priority', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<link rel="preload">` and `<link rel="prefetch">`?',
                'explanation' => '`preload` fetches resources needed for the current page with high priority — the browser is certain it will need them soon. `prefetch` fetches resources likely needed for future navigations with low priority, in idle time. A preloaded resource not used within a few seconds triggers a browser warning. Use preload for current-page critical assets and prefetch for next-page resources.',
                'options'     => [
                    ['text' => 'preload is for current-page critical resources (high priority); prefetch is for future pages (low priority)', 'correct' => true],
                    ['text' => 'They are identical — both fetch resources before they are needed', 'correct' => false],
                    ['text' => 'prefetch is faster because it uses a service worker; preload uses the main thread', 'correct' => false],
                    ['text' => 'preload works only for scripts; prefetch works for all resource types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `rel="canonical"` and why is it used?',
                'explanation' => '`<link rel="canonical" href="https://example.com/page">` tells search engines which URL is the preferred (canonical) version of a page when duplicate or near-duplicate versions exist (e.g., HTTP vs HTTPS, www vs non-www, URL parameters). It consolidates ranking signals to the canonical URL, preventing duplicate content penalties in SEO.',
                'options'     => [
                    ['text' => 'Tells search engines the preferred URL for a page to prevent duplicate content issues', 'correct' => true],
                    ['text' => 'Loads the canonical (minified) version of a stylesheet', 'correct' => false],
                    ['text' => 'Marks the page as the original source for syndicated content on other sites', 'correct' => false],
                    ['text' => 'Links to the site\'s canonical API documentation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Open Graph meta tags and why are they important?',
                'explanation' => 'Open Graph (OG) meta tags control how a page appears when shared on social media platforms (Facebook, Twitter, LinkedIn). Key tags: `og:title`, `og:description`, `og:image`, and `og:url`. Without OG tags, platforms generate their own (often poor) previews. They are placed in `<head>`: `<meta property="og:title" content="My Page Title">`.',
                'options'     => [
                    ['text' => 'Meta tags that control how a page looks when shared on social media (title, description, image)', 'correct' => true],
                    ['text' => 'Tags that link to open-source graph libraries for data visualisation', 'correct' => false],
                    ['text' => 'HTML attributes that expose the page to public APIs without authentication', 'correct' => false],
                    ['text' => 'Search engine meta tags for ranking signals, different from the standard description tag', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is JSON-LD structured data and how is it added to an HTML page?',
                'explanation' => 'JSON-LD (JavaScript Object Notation for Linked Data) embeds structured data using schema.org vocabulary in a `<script type="application/ld+json">` block inside `<head>`. Search engines read it to understand page content and generate rich results (star ratings, FAQs, breadcrumbs, events). It is preferred over Microdata or RDFa because it does not mix with HTML markup and is easy to add/maintain.',
                'options'     => [
                    ['text' => 'Machine-readable schema.org metadata in a <script type="application/ld+json"> block in <head>', 'correct' => true],
                    ['text' => 'A JSON config file linked via <link> that controls page SEO settings', 'correct' => false],
                    ['text' => 'A JavaScript library for generating HTML with linked data attributes', 'correct' => false],
                    ['text' => 'Data attributes (data-*) that follow the Linked Data specification', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────
            [
                'question'    => 'What is the Shadow DOM in HTML?',
                'explanation' => 'The Shadow DOM is a browser technology that attaches a separate, encapsulated DOM tree to a custom element. Styles and scripts inside the shadow tree do not leak out, and external styles do not leak in. This is the foundation of Web Components\' style encapsulation and allows creating truly isolated UI components.',
                'options'     => [
                    ['text' => 'An encapsulated DOM tree attached to an element, providing style and DOM isolation', 'correct' => true],
                    ['text' => 'The invisible version of the virtual DOM used by React', 'correct' => false],
                    ['text' => 'A copy of the DOM used for diffing and patching', 'correct' => false],
                    ['text' => 'A browser extension API for manipulating DOM without detection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Web Components?',
                'explanation' => 'Web Components are a set of native browser APIs for creating reusable custom HTML elements. They consist of three main specifications: Custom Elements (define new HTML tags), Shadow DOM (encapsulated styles and DOM), and HTML Templates (`<template>` and `<slot>`). Web Components work in any framework or without one.',
                'options'     => [
                    ['text' => 'A set of native browser APIs for creating reusable, encapsulated custom elements', 'correct' => true],
                    ['text' => 'React\'s term for class-based components', 'correct' => false],
                    ['text' => 'Third-party UI libraries like Bootstrap or Material UI', 'correct' => false],
                    ['text' => 'Components built with WebAssembly for maximum performance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between localStorage and sessionStorage?',
                'explanation' => 'Both localStorage and sessionStorage store key-value pairs in the browser. localStorage persists across browser sessions — it survives tab/window close and browser restarts until explicitly cleared. sessionStorage is scoped to a single tab/window session — it is cleared when the tab is closed. Both are synchronous and limited to ~5MB per origin.',
                'options'     => [
                    ['text' => 'localStorage persists until explicitly cleared; sessionStorage is cleared when the tab closes', 'correct' => true],
                    ['text' => 'sessionStorage is larger; localStorage is limited to 4KB', 'correct' => false],
                    ['text' => 'localStorage is sent to the server with each request; sessionStorage is not', 'correct' => false],
                    ['text' => 'They are identical — just different names for the same API', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is progressive enhancement in web development?',
                'explanation' => 'Progressive enhancement is a strategy that starts with a solid, accessible baseline (valid HTML with core content) that works for all browsers and users, then layers on enhancements — CSS styling, then JavaScript interactivity — for browsers that support them. It ensures content is accessible even when JavaScript fails or CSS does not load.',
                'options'     => [
                    ['text' => 'Building from a working HTML baseline, then layering CSS and JavaScript enhancements', 'correct' => true],
                    ['text' => 'Progressively loading assets as the user scrolls down the page', 'correct' => false],
                    ['text' => 'An approach that targets only the latest browsers with full features', 'correct' => false],
                    ['text' => 'Incrementally adding new features to a live production site', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Intersection Observer API?',
                'explanation' => 'The Intersection Observer API provides an efficient way to observe when an element enters or exits the viewport (or another element). It is used for lazy loading images, infinite scroll, triggering animations, and ad visibility tracking. It replaces scroll event listeners, which are expensive because they run on the main thread.',
                'options'     => [
                    ['text' => 'An API for efficiently detecting when elements enter or exit the viewport', 'correct' => true],
                    ['text' => 'An API for observing changes to an element\'s CSS properties', 'correct' => false],
                    ['text' => 'A way to detect when two DOM elements overlap each other', 'correct' => false],
                    ['text' => 'An observer pattern implementation built into modern browsers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between the HTML `<canvas>` and `<svg>` elements?',
                'explanation' => 'Canvas is a bitmap-based immediate-mode drawing surface — you draw pixels imperatively using JavaScript, and the canvas has no DOM for individual shapes. SVG is a vector-based retained-mode format — shapes are DOM elements that can be styled with CSS and manipulated with JavaScript. Canvas is better for animations/games; SVG is better for scalable, interactive diagrams.',
                'options'     => [
                    ['text' => 'Canvas is pixel-based (no DOM); SVG is vector-based with each shape as a DOM element', 'correct' => true],
                    ['text' => 'Canvas is for 3D graphics only; SVG is for 2D graphics only', 'correct' => false],
                    ['text' => 'SVG uses JavaScript exclusively; canvas works with HTML attributes', 'correct' => false],
                    ['text' => 'Canvas is a W3C standard; SVG is a proprietary Adobe format', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `rel="noopener noreferrer"` attribute on `<a target="_blank">` links?',
                'explanation' => '`noopener` prevents the new tab from accessing the opener\'s `window.opener` object, which could be used in phishing attacks (tabnabbing — the new page redirects the original tab). `noreferrer` additionally prevents the browser from sending the Referer header. Modern browsers apply noopener automatically for _blank links, but explicit declaration is still best practice.',
                'options'     => [
                    ['text' => 'Prevents the new tab from accessing window.opener and stops sending the Referer header', 'correct' => true],
                    ['text' => 'Tells the browser not to open the link in a new tab', 'correct' => false],
                    ['text' => 'Marks the link as non-indexable by search engines', 'correct' => false],
                    ['text' => 'Prevents JavaScript from intercepting the link click event', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are microdata and structured data in HTML?',
                'explanation' => 'Structured data (microdata, JSON-LD, RDFa) embeds machine-readable metadata into HTML pages, following schemas from schema.org. Search engines like Google use it to understand content and generate rich results (star ratings, FAQs, breadcrumbs). JSON-LD in a `<script type="application/ld+json">` block is the recommended approach.',
                'options'     => [
                    ['text' => 'Machine-readable metadata (schema.org) embedded in HTML to help search engines understand content', 'correct' => true],
                    ['text' => 'Miniaturized HTML components optimized for low-bandwidth connections', 'correct' => false],
                    ['text' => 'Data attributes (data-*) used for micro-interactions in JavaScript', 'correct' => false],
                    ['text' => 'A specification for embedding CSV data directly in HTML tables', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `contenteditable` attribute in HTML?',
                'explanation' => '`contenteditable="true"` makes an HTML element editable by the user — they can click into it and type as if it were a text input. This applies to any element, not just form controls. It is used for rich-text editors (like Google Docs, Notion) where the editing surface is a styled `<div>` rather than a `<textarea>`.',
                'options'     => [
                    ['text' => 'Makes any HTML element directly editable by the user in the browser', 'correct' => true],
                    ['text' => 'Restricts editing of an element to authorised users only', 'correct' => false],
                    ['text' => 'Enables CSS editing tools in the browser DevTools for that element', 'correct' => false],
                    ['text' => 'Marks a form field as read-only with the ability to copy text', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `<base>` element in HTML?',
                'explanation' => 'The `<base>` element specifies a base URL for all relative URLs in the document: `<base href="https://example.com/blog/">`. Every relative link, image src, and script src on the page is resolved relative to the base URL. It also accepts a `target` attribute that sets the default target for all links. There can only be one `<base>` element, in the `<head>`.',
                'options'     => [
                    ['text' => 'Sets the base URL that all relative URLs on the page are resolved against', 'correct' => true],
                    ['text' => 'Defines the root element of the DOM tree', 'correct' => false],
                    ['text' => 'Links to the base CSS stylesheet for the entire site', 'correct' => false],
                    ['text' => 'Declares the base programming language for embedded scripts', 'correct' => false],
                ],
            ],
            // ── Additions (23 more) ───────────────────────────────────────
            [
                'question'    => 'What are WAI-ARIA live regions and when should you use them?',
                'explanation' => 'ARIA live regions (`aria-live="polite"` or `"assertive"`) tell screen readers to announce dynamic content changes without the user having to navigate to the updated element. `polite` waits for the user to finish their current activity; `assertive` interrupts immediately. Common use cases: status messages, form errors injected dynamically, notifications, and chat messages — any content that updates without a page reload.',
                'options'     => [
                    ['text' => 'Markup that tells screen readers to announce dynamic DOM updates without user navigation', 'correct' => true],
                    ['text' => 'CSS regions that animate when content changes, for visual accessibility', 'correct' => false],
                    ['text' => 'JavaScript events that broadcast DOM changes to analytics services', 'correct' => false],
                    ['text' => 'Browser regions that stay visible even when the user scrolls away', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is focus trapping and why is it required for `role="dialog"` elements?',
                'explanation' => 'Focus trapping keeps keyboard focus inside a modal dialog while it is open. Without it, Tab can move focus behind the modal to the page, confusing sighted keyboard users. The ARIA authoring practices require that when a modal opens, focus moves into it, Tab/Shift+Tab cycles only within it, and Escape closes it and returns focus to the trigger. This is implemented in JavaScript — HTML alone cannot trap focus.',
                'options'     => [
                    ['text' => 'Keeping keyboard Tab focus inside an open dialog so users cannot tab to content behind it', 'correct' => true],
                    ['text' => 'Preventing the browser from capturing focus events for security', 'correct' => false],
                    ['text' => 'A CSS technique that visually focuses a dialog using box-shadow', 'correct' => false],
                    ['text' => 'An ARIA attribute that automatically traps focus without JavaScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a skip navigation link and why is it important?',
                'explanation' => 'A skip navigation link is the first focusable element on a page, typically visually hidden until focused: `<a href="#main-content" class="skip-link">Skip to main content</a>`. It allows keyboard and screen reader users to jump past repeated navigation menus directly to the page\'s main content, avoiding repetitive tabbing through every nav item on every page.',
                'options'     => [
                    ['text' => 'A visually hidden link at the top that lets keyboard users jump past navigation to main content', 'correct' => true],
                    ['text' => 'A link that removes the navigation bar for users who prefer a clean layout', 'correct' => false],
                    ['text' => 'A back-button link that skips the browser history and returns to the home page', 'correct' => false],
                    ['text' => 'An ARIA role that marks navigation as optional for assistive technologies', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you register a custom HTML element using the Custom Elements API?',
                'explanation' => '`customElements.define("my-element", MyElementClass)` registers a new custom element. `MyElementClass` must extend `HTMLElement` (or a specific built-in). The tag name must contain a hyphen (to avoid clashing with future HTML elements). Lifecycle callbacks — `connectedCallback`, `disconnectedCallback`, `attributeChangedCallback` — let the element react to DOM events and attribute changes.',
                'options'     => [
                    ['text' => 'customElements.define("tag-name", class extends HTMLElement {})', 'correct' => true],
                    ['text' => 'document.registerElement("tag-name", { prototype: HTMLElement })', 'correct' => false],
                    ['text' => 'HTML.createComponent("tag-name", options)', 'correct' => false],
                    ['text' => 'window.customElement = new HTMLElement("tag-name")', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `<slot>` element in the Shadow DOM?',
                'explanation' => '`<slot>` is a placeholder inside a shadow tree where the host element\'s Light DOM children are projected. Named slots (`<slot name="header">`) allow specific content to be directed to specific locations. This is the composition mechanism of Web Components — the component defines the structure, and the user provides content that flows into the slots without breaking encapsulation.',
                'options'     => [
                    ['text' => 'A placeholder in a shadow DOM template where the host\'s Light DOM children are projected', 'correct' => true],
                    ['text' => 'An HTML element that creates a time slot in a scheduling UI', 'correct' => false],
                    ['text' => 'A reserved slot for injecting styles into a shadow root', 'correct' => false],
                    ['text' => 'A named anchor point for CSS Grid template areas', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is declarative Shadow DOM and how does it differ from imperative Shadow DOM?',
                'explanation' => 'Declarative Shadow DOM uses the `shadowrootmode` attribute on a `<template>` element: `<template shadowrootmode="open">`. The browser attaches the shadow root during HTML parsing — no JavaScript is needed. This enables server-side rendering of Web Components and makes them available before JavaScript loads. Imperative Shadow DOM requires `element.attachShadow({ mode: "open" })` in JavaScript.',
                'options'     => [
                    ['text' => 'Declarative Shadow DOM uses shadowrootmode on <template> for SSR without JS; imperative uses attachShadow() in JS', 'correct' => true],
                    ['text' => 'Declarative Shadow DOM is the older approach; imperative is the HTML5 standard', 'correct' => false],
                    ['text' => 'They are identical in behaviour — only the syntax differs', 'correct' => false],
                    ['text' => 'Declarative Shadow DOM requires a polyfill; imperative is natively supported', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `adoptedStyleSheets` API and how does it improve Shadow DOM styling?',
                'explanation' => '`adoptedStyleSheets` allows sharing a `CSSStyleSheet` object across multiple shadow roots and the main document without duplicating the CSS text. Instead of injecting `<style>` tags (which are parsed per shadow root), you create one `CSSStyleSheet`, populate it with `replaceSync()`, and assign it: `shadowRoot.adoptedStyleSheets = [sheet]`. This reduces memory and parse overhead for components with many instances.',
                'options'     => [
                    ['text' => 'Allows sharing a single CSSStyleSheet object across shadow roots without duplicating CSS text', 'correct' => true],
                    ['text' => 'Adopts external stylesheets from the Light DOM into the shadow root automatically', 'correct' => false],
                    ['text' => 'An API for downloading remote CSS files into a shadow root at runtime', 'correct' => false],
                    ['text' => 'A polyfill API that makes Shadow DOM stylesheets compatible with older browsers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the ElementInternals API used for?',
                'explanation' => '`ElementInternals` (obtained via `this.attachInternals()` in a custom element) lets custom elements participate in HTML forms natively. It provides `setValidity()`, `setFormValue()`, and `reportValidity()` so custom elements work with the Constraint Validation API and appear in `form.elements`. It also provides `ariaRole` and other ARIA properties for accessibility without external attributes.',
                'options'     => [
                    ['text' => 'Lets custom elements integrate with HTML forms (validation, value submission) and set ARIA properties', 'correct' => true],
                    ['text' => 'An API for reading internal browser rendering metrics of an element', 'correct' => false],
                    ['text' => 'Provides access to internal DOM mutation events not available via MutationObserver', 'correct' => false],
                    ['text' => 'Allows JavaScript to modify internal browser styles applied to native elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the HTML Popover API and how does it differ from a modal dialog?',
                'explanation' => 'The Popover API (`popover` attribute + `popovertarget` attribute or `showPopover()` JS method) creates lightweight dismissible overlays anchored to a trigger. Unlike `<dialog>`, a popover is non-modal by default — the rest of the page remains interactive. It renders in the top layer (above everything, including `z-index`), has built-in light dismiss (click outside to close), and requires no JavaScript for basic use.',
                'options'     => [
                    ['text' => 'A native non-modal overlay in the top layer with light dismiss; dialog is modal and blocks interaction', 'correct' => true],
                    ['text' => 'A CSS pseudo-element that creates tooltip-style overlays without any HTML changes', 'correct' => false],
                    ['text' => 'A replacement for <dialog> that removes the need for focus trapping', 'correct' => false],
                    ['text' => 'An API exclusively for mobile browsers to show context menus', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `<dialog>` with `show()` vs `showModal()`?',
                'explanation' => '`dialog.show()` opens the dialog as a non-modal — the rest of the page is still interactive and the dialog does not have a backdrop. `dialog.showModal()` opens it as a true modal in the top layer — it adds a `::backdrop` pseudo-element, traps focus inside, and the user must close it before interacting with the rest of the page. `showModal()` is the correct choice for confirmation dialogs and forms requiring user action.',
                'options'     => [
                    ['text' => 'show() opens a non-modal dialog; showModal() opens a blocking modal with backdrop and focus trap', 'correct' => true],
                    ['text' => 'They are identical — showModal() is just an alias for show()', 'correct' => false],
                    ['text' => 'show() is for inline dialogs; showModal() opens in a new browser window', 'correct' => false],
                    ['text' => 'showModal() requires the dialog element to have role="alertdialog"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are form-associated custom elements?',
                'explanation' => 'Form-associated custom elements are custom elements that opt into form participation by setting `static formAssociated = true` and using `ElementInternals`. They appear in `form.elements`, their values are included in form submission, and they work with `required`, `disabled`, and Constraint Validation. This allows developers to build custom inputs (e.g., a styled date picker) that behave exactly like native `<input>` elements from the form\'s perspective.',
                'options'     => [
                    ['text' => 'Custom elements that opt into form participation via formAssociated=true and ElementInternals', 'correct' => true],
                    ['text' => 'Native form elements that have been extended with custom attributes', 'correct' => false],
                    ['text' => 'Custom elements that must be placed directly inside a <form> to function', 'correct' => false],
                    ['text' => 'Any custom element that handles the submit event on a form', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Speculation Rules API in HTML?',
                'explanation' => 'The Speculation Rules API (defined via `<script type="speculationrules">` JSON) instructs the browser to prefetch or prerender future page navigations speculatively. Prerendering fully loads and executes the target page in a hidden tab — navigation becomes near-instant. Unlike `<link rel="prefetch">`, speculation rules support prerendering and can be conditioned on hover/pointer events, providing much faster perceived navigation.',
                'options'     => [
                    ['text' => 'A JSON-based API that instructs the browser to prefetch or prerender future pages for instant navigation', 'correct' => true],
                    ['text' => 'A browser API for speculating the user\'s next action using machine learning', 'correct' => false],
                    ['text' => 'Rules that allow the browser to speculatively execute JavaScript before user interaction', 'correct' => false],
                    ['text' => 'A server-sent event API for pushing speculative data updates to the page', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the View Transitions API and what problem does it solve?',
                'explanation' => 'The View Transitions API (`document.startViewTransition(() => updateDOM())`) captures a screenshot of the current state, updates the DOM, then animates between the two states using CSS. It solves the problem of jarring instant DOM updates by providing smooth, native-like animated transitions — previously only possible with complex JavaScript orchestration. Same-document view transitions are widely supported; cross-document transitions require the `@view-transition` CSS rule.',
                'options'     => [
                    ['text' => 'Animates between two DOM states (before/after update) with native CSS transitions, removing custom JS animation logic', 'correct' => true],
                    ['text' => 'An API for transitioning between CSS themes without a page reload', 'correct' => false],
                    ['text' => 'A CSS-only feature for animating page scroll position between sections', 'correct' => false],
                    ['text' => 'Provides hardware-accelerated transitions using the GPU compositor thread', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `fetchpriority` attribute on resource elements?',
                'explanation' => '`fetchpriority="high"` or `"low"` hints to the browser the relative priority for fetching a resource. On `<img fetchpriority="high">`, it boosts the priority of the Largest Contentful Paint image so it loads sooner. On `<script fetchpriority="low">`, it deprioritises non-critical scripts. It does not change network behaviour — it adjusts where the resource sits in the browser\'s internal fetch queue relative to other resources at the same priority level.',
                'options'     => [
                    ['text' => 'Hints the browser to fetch the resource at high or low priority relative to other resources', 'correct' => true],
                    ['text' => 'Sets the HTTP priority header sent with the resource request', 'correct' => false],
                    ['text' => 'Instructs the CDN to serve the resource from a higher-tier cache', 'correct' => false],
                    ['text' => 'Forces the browser to fetch the resource before parsing the HTML', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `dns-prefetch` and `preconnect` resource hints?',
                'explanation' => '`<link rel="dns-prefetch" href="https://api.example.com">` resolves only the DNS for a domain early — cheap and low risk. `<link rel="preconnect" href="https://api.example.com">` performs the full connection: DNS + TCP handshake + TLS negotiation — eliminating more latency but using more CPU and browser connections. Use `preconnect` for origins you are certain will be used soon; use `dns-prefetch` as a cheaper fallback or for less certain origins.',
                'options'     => [
                    ['text' => 'dns-prefetch resolves DNS only; preconnect does DNS + TCP + TLS for maximum connection savings', 'correct' => true],
                    ['text' => 'They are identical; dns-prefetch is the deprecated name for preconnect', 'correct' => false],
                    ['text' => 'preconnect only resolves DNS; dns-prefetch performs the full handshake', 'correct' => false],
                    ['text' => 'dns-prefetch is for same-origin resources; preconnect is for cross-origin only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Content Security Policy nonce and how is it used in HTML?',
                'explanation' => 'A CSP nonce is a random, single-use token generated server-side for each response. It is added to inline scripts: `<script nonce="abc123">` and to the `Content-Security-Policy` header: `script-src \'nonce-abc123\'`. The browser only executes inline scripts whose nonce matches the header value. This allows specific trusted inline scripts while blocking injected scripts (XSS), without needing `\'unsafe-inline\'`.',
                'options'     => [
                    ['text' => 'A server-generated random token on <script nonce="…"> that CSP uses to allow specific inline scripts', 'correct' => true],
                    ['text' => 'A permanent API key embedded in HTML to authenticate script requests', 'correct' => false],
                    ['text' => 'A hash value the browser generates for each script to verify its integrity', 'correct' => false],
                    ['text' => 'A browser-generated token used to prevent CSRF attacks on form submissions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Subresource Integrity (SRI) and how is it used?',
                'explanation' => 'SRI allows browsers to verify that a fetched resource (script or stylesheet) has not been tampered with. You add a `integrity` attribute containing a base64-encoded cryptographic hash of the expected file content: `<script src="https://cdn.example.com/lib.js" integrity="sha384-abc…" crossorigin="anonymous">`. If the hash does not match the downloaded file, the browser blocks the resource. It is essential for CDN-hosted resources.',
                'options'     => [
                    ['text' => 'Verifies fetched resources match an expected hash; blocks them if tampered', 'correct' => true],
                    ['text' => 'Encrypts resources in transit using the browser\'s built-in key store', 'correct' => false],
                    ['text' => 'Ensures sub-resources load from the same origin as the main document', 'correct' => false],
                    ['text' => 'A browser API for signing HTML elements to prove they came from the server', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `crossorigin` attribute on `<img>`, `<script>`, and `<link>` elements do?',
                'explanation' => '`crossorigin="anonymous"` makes the browser fetch the resource with a CORS request but without credentials (no cookies, no HTTP auth). `crossorigin="use-credentials"` includes credentials. Setting `crossorigin` is required when you want to read cross-origin image pixel data on a `<canvas>`, use SRI on a cross-origin script, or read response headers from a cross-origin fetch. Without it, CORS requests from these elements are opaque.',
                'options'     => [
                    ['text' => 'Triggers a CORS request for the resource; anonymous = no credentials, use-credentials = send cookies', 'correct' => true],
                    ['text' => 'Marks the resource as cross-origin so the browser skips same-origin policy checks', 'correct' => false],
                    ['text' => 'Loads the resource from a CDN mirror instead of the specified origin', 'correct' => false],
                    ['text' => 'Prevents the browser from sending the resource to cross-origin iframes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `referrerpolicy` attribute control on HTML elements?',
                'explanation' => '`referrerpolicy` controls what information the browser includes in the `Referer` request header when navigating or fetching. Values include `no-referrer` (send nothing), `origin` (send only the origin), `strict-origin-when-cross-origin` (the browser default — full URL for same-origin, origin only for cross-origin HTTPS→HTTPS, nothing for HTTPS→HTTP), and `unsafe-url` (always send full URL). It can be set on `<a>`, `<img>`, `<script>`, `<link>`, and `<iframe>` elements.',
                'options'     => [
                    ['text' => 'Controls how much Referer header information is sent when the resource is requested', 'correct' => true],
                    ['text' => 'Sets the origin that is allowed to refer to the current page', 'correct' => false],
                    ['text' => 'Defines a fallback URL if the primary resource fails to load', 'correct' => false],
                    ['text' => 'Prevents the browser from caching the resource based on the referrer origin', 'correct' => false],
                ],
            ],
        ];
    }
}
