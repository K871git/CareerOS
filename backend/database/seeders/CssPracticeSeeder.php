<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class CssPracticeSeeder extends Seeder
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
            ['slug' => 'css'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'CSS',
                'description'       => 'Cascading Style Sheets — master layouts, selectors, animations, and responsive design patterns used in modern web development.',
                'display_order'     => 5,
            ]
        );

        $levels = [
            [
                'title'         => 'CSS Basics — Junior',
                'slug'          => 'css-junior',
                'description'   => 'Box model, selectors, positioning, and fundamental CSS layout. Perfect for junior-level interview preparation.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'CSS Intermediate',
                'slug'          => 'css-intermediate',
                'description'   => 'Flexbox, Grid, custom properties, media queries, and transitions. For developers targeting mid-level roles.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'CSS Advanced',
                'slug'          => 'css-advanced',
                'description'   => 'Specificity, stacking contexts, animations, container queries, and performance. Essential for senior developer interviews.',
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

            foreach ($levelData['questions'] as $qData) {
                $exists = Question::where('topic_id', $topic->id)
                    ->where('question', $qData['question'])
                    ->exists();
                if ($exists) {
                    continue;
                }

                $question = Question::create([
                    'topic_id'    => $topic->id,
                    'type'        => 'MCQ',
                    'difficulty'  => $levelData['difficulty'],
                    'question'    => $qData['question'],
                    'explanation' => $qData['explanation'],
                ]);

                foreach ($qData['options'] as $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt['text'],
                        'is_correct'  => $opt['correct'],
                    ]);
                }
            }
        }

        $this->command->info('CSS Practice seeded: 1 subject, 3 topics, ~100 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            // --- original 10 ---
            [
                'question'    => 'What does the CSS Box Model consist of?',
                'explanation' => 'Every HTML element is represented as a rectangular box consisting of four layers from inside out: content (text/images), padding (space inside the border), border (the visible outline), and margin (space outside the border). Understanding the box model is fundamental to controlling layout and spacing.',
                'options'     => [
                    ['text' => 'Content, padding, border, and margin', 'correct' => true],
                    ['text' => 'Width, height, color, and position', 'correct' => false],
                    ['text' => 'Element, class, id, and style', 'correct' => false],
                    ['text' => 'Top, bottom, left, and right', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between CSS class selectors and ID selectors?',
                'explanation' => 'Class selectors (`.className`) can be applied to multiple elements and reused across a page. ID selectors (`#idName`) should be unique — only one element on the page should have a given ID. IDs have higher specificity than classes, meaning their styles take precedence when both target the same element.',
                'options'     => [
                    ['text' => 'Classes can be reused on many elements; IDs must be unique per page', 'correct' => true],
                    ['text' => 'IDs are for JavaScript only; classes are for CSS only', 'correct' => false],
                    ['text' => 'Classes are inline styles; IDs are external stylesheet styles', 'correct' => false],
                    ['text' => 'They are identical — just different naming preferences', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `display: none` and `visibility: hidden`?',
                'explanation' => '`display: none` completely removes the element from the document flow — it takes up no space and is invisible. `visibility: hidden` hides the element visually but preserves the space it occupies in the layout. Use `display: none` to collapse space; use `visibility: hidden` to hide without shifting other elements.',
                'options'     => [
                    ['text' => 'display: none removes the element from layout; visibility: hidden hides but keeps the space', 'correct' => true],
                    ['text' => 'visibility: hidden removes the element from layout; display: none keeps the space', 'correct' => false],
                    ['text' => 'They are identical — both remove the element from the page completely', 'correct' => false],
                    ['text' => 'display: none only works on block elements; visibility: hidden works on all elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is CSS specificity?',
                'explanation' => 'Specificity is a weight system that determines which CSS rule applies when multiple rules target the same element. The hierarchy is: inline styles > ID selectors > class/pseudo-class/attribute selectors > element selectors. When specificity is equal, the last rule in the source order wins.',
                'options'     => [
                    ['text' => 'A weight system that determines which CSS rule wins when multiple rules apply', 'correct' => true],
                    ['text' => 'The speed at which CSS is parsed by the browser', 'correct' => false],
                    ['text' => 'The number of CSS properties defined in a stylesheet', 'correct' => false],
                    ['text' => 'A measure of how specific a CSS colour value is', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `position: absolute` do?',
                'explanation' => '`position: absolute` removes an element from the normal document flow and positions it relative to the nearest ancestor with `position` set to `relative`, `absolute`, `fixed`, or `sticky`. If no such ancestor exists, it is positioned relative to the initial containing block (the viewport). Top, right, bottom, and left offsets can then be used to place it precisely.',
                'options'     => [
                    ['text' => 'Positions the element relative to its nearest positioned ancestor, removed from normal flow', 'correct' => true],
                    ['text' => 'Positions the element relative to the viewport and scrolls with the page', 'correct' => false],
                    ['text' => 'Locks the element in place relative to the browser window even when scrolling', 'correct' => false],
                    ['text' => 'Positions the element in its default location in the document flow', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `em` and `rem` CSS units?',
                'explanation' => '`em` is relative to the font size of the element\'s parent (or the element itself for non-font properties). `rem` (root em) is relative to the root `<html>` element\'s font size, making it more predictable and easier to manage globally. Using `rem` for font sizes and spacing creates a consistent scaling system.',
                'options'     => [
                    ['text' => 'em is relative to the parent\'s font size; rem is relative to the root <html> font size', 'correct' => true],
                    ['text' => 'rem is relative to the parent; em is relative to the root', 'correct' => false],
                    ['text' => 'em is for font sizes only; rem is for spacing only', 'correct' => false],
                    ['text' => 'They are identical — both equal the current element\'s font size', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `z-index` control in CSS?',
                'explanation' => '`z-index` controls the stacking order of positioned elements along the z-axis (depth). Higher values stack on top of lower values. `z-index` only works on elements with a `position` value other than `static` (i.e., relative, absolute, fixed, or sticky). Elements in the same stacking context are compared; different stacking contexts are compared as units.',
                'options'     => [
                    ['text' => 'Controls the stacking order of positioned elements along the z-axis', 'correct' => true],
                    ['text' => 'Sets the zoom level of an element', 'correct' => false],
                    ['text' => 'Controls the horizontal position of an element', 'correct' => false],
                    ['text' => 'Sets how many elements deep in the DOM an element appears', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you center an element horizontally using CSS Flexbox?',
                'explanation' => 'On a flex container, `justify-content: center` centers flex items along the main axis (horizontally by default). To also center vertically, add `align-items: center` and set a height. For a single element that is a flex item, `margin: auto` also centers it because flex respects auto margins on all sides.',
                'options'     => [
                    ['text' => 'Set display: flex and justify-content: center on the parent container', 'correct' => true],
                    ['text' => 'Set margin: 0 auto and display: inline-block on the element itself', 'correct' => false],
                    ['text' => 'Set align-items: center on the element itself', 'correct' => false],
                    ['text' => 'Set position: relative and left: 50% on the element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `box-sizing: border-box` do?',
                'explanation' => 'By default (`content-box`), `width` and `height` apply to the content area only — padding and border are added on top. With `border-box`, padding and border are included in the specified width and height. This makes sizing elements far more intuitive and predictable, which is why `* { box-sizing: border-box; }` is a common CSS reset.',
                'options'     => [
                    ['text' => 'Includes padding and border in the element\'s specified width and height', 'correct' => true],
                    ['text' => 'Adds a box shadow to all sides of an element', 'correct' => false],
                    ['text' => 'Prevents the element from shrinking below its content size', 'correct' => false],
                    ['text' => 'Makes the element\'s size determined by the browser automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a CSS pseudo-class?',
                'explanation' => 'Pseudo-classes target elements based on their state or position, without requiring extra HTML attributes. Examples: `:hover` (mouse over), `:focus` (keyboard/mouse focus), `:first-child` (first child of parent), `:nth-child(n)` (specific position), `:checked` (checked input). They are written with a single colon after the selector.',
                'options'     => [
                    ['text' => 'A selector that targets elements based on their state or position, like :hover or :focus', 'correct' => true],
                    ['text' => 'A CSS property that mimics the behaviour of a class selector', 'correct' => false],
                    ['text' => 'A fake class that is not part of the HTML document', 'correct' => false],
                    ['text' => 'A class that is added dynamically by JavaScript', 'correct' => false],
                ],
            ],
            // --- 23 additions ---
            [
                'question'    => 'What is the difference between the `background-color` and `color` CSS properties?',
                'explanation' => '`color` sets the foreground colour of text and text decorations inside an element. `background-color` sets the colour of the element\'s background area behind the content. Both accept any valid CSS colour value (hex, rgb, hsl, named). Applying only `background-color` will not affect text colour and vice versa.',
                'options'     => [
                    ['text' => 'color sets the text colour; background-color sets the element\'s background area colour', 'correct' => true],
                    ['text' => 'background-color sets the text colour; color sets the background', 'correct' => false],
                    ['text' => 'They are interchangeable — both set the overall element colour', 'correct' => false],
                    ['text' => 'color only works on inline elements; background-color only on block elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which CSS unit is relative to the viewport width?',
                'explanation' => '`vw` (viewport width) is relative to 1% of the viewport\'s width. `vh` is relative to 1% of the viewport\'s height. So `width: 50vw` means 50% of the browser window width regardless of the parent element\'s size. These units are useful for full-screen sections and fluid typography that scales with the window.',
                'options'     => [
                    ['text' => 'vw', 'correct' => true],
                    ['text' => 'em', 'correct' => false],
                    ['text' => 'pt', 'correct' => false],
                    ['text' => 'ex', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are valid CSS `font-weight` values for bold text?',
                'explanation' => '`font-weight` accepts numeric values from 100 to 900 in increments of 100, where 400 equals normal and 700 equals bold. It also accepts keywords: `normal` (400), `bold` (700), `lighter` (relative to parent), and `bolder` (relative to parent). Not all weights are supported by every font — missing weights are mapped to the nearest available weight.',
                'options'     => [
                    ['text' => '700 or the keyword bold', 'correct' => true],
                    ['text' => '500 or the keyword strong', 'correct' => false],
                    ['text' => '900 is the only numeric bold value; no keywords are accepted', 'correct' => false],
                    ['text' => 'bold and heavy are both valid keywords for bold weight', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `text-align: justify` do in CSS?',
                'explanation' => '`text-align: justify` stretches each line of text so that it reaches both the left and right edges of the container by adding space between words. The last line is exempt and remains left-aligned. Other values are `left`, `right`, `center`, and `start`/`end` (writing-mode-aware). Justification can look poor on narrow columns with long words.',
                'options'     => [
                    ['text' => 'Stretches lines so text aligns to both left and right edges of the container', 'correct' => true],
                    ['text' => 'Centers text horizontally in the container', 'correct' => false],
                    ['text' => 'Aligns text to the right margin only', 'correct' => false],
                    ['text' => 'Removes all horizontal text alignment so the browser decides', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the shorthand syntax for the CSS `border` property?',
                'explanation' => 'The `border` shorthand sets `border-width`, `border-style`, and `border-color` in one declaration: `border: 2px solid #333`. All three values can appear in any order, but specifying style is required for a border to appear at all. Omitting colour defaults to the element\'s `color` value. Individual sides can be set with `border-top`, `border-right`, etc.',
                'options'     => [
                    ['text' => 'border: width style color — e.g., border: 2px solid #333', 'correct' => true],
                    ['text' => 'border: color width style — e.g., border: #333 2px solid', 'correct' => false],
                    ['text' => 'border requires three separate properties; no shorthand exists', 'correct' => false],
                    ['text' => 'border: style width — color is always inherited and cannot be set', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you horizontally center a block element using `margin: auto`?',
                'explanation' => 'Setting `margin-left: auto` and `margin-right: auto` (shorthand: `margin: 0 auto`) on a block element with a defined `width` causes the browser to distribute the remaining horizontal space equally on both sides, centering the element. Without a fixed width, block elements expand to fill their container and auto margins have no effect.',
                'options'     => [
                    ['text' => 'Give the element a fixed width and set margin-left and margin-right to auto', 'correct' => true],
                    ['text' => 'Set margin: auto on any block element regardless of width', 'correct' => false],
                    ['text' => 'margin: auto centers both horizontally and vertically for all elements', 'correct' => false],
                    ['text' => 'margin: auto only works with inline elements, not block elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'When `padding` is set with four values — `padding: 10px 20px 30px 40px` — in what order are the sides applied?',
                'explanation' => 'Four-value padding shorthand follows a clockwise order starting from the top: top, right, bottom, left — a useful mnemonic is "TRouBLe" (Top, Right, Bottom, Left). So `padding: 10px 20px 30px 40px` means top 10 px, right 20 px, bottom 30 px, left 40 px. Two values set top/bottom then left/right; three values set top, left/right, bottom.',
                'options'     => [
                    ['text' => 'Top, right, bottom, left (clockwise from top)', 'correct' => true],
                    ['text' => 'Left, right, top, bottom', 'correct' => false],
                    ['text' => 'Top, bottom, left, right', 'correct' => false],
                    ['text' => 'Left, top, right, bottom', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `width` and `max-width` in CSS?',
                'explanation' => '`width` sets a fixed width for the element. `max-width` sets the maximum width the element can grow to — if the container is narrower, the element shrinks. Using `max-width` with no fixed `width` makes elements responsive: they fill their container up to the limit. A common pattern is `max-width: 1200px; margin: 0 auto` for centered page wrappers.',
                'options'     => [
                    ['text' => 'width is a fixed size; max-width allows the element to shrink below the value on small screens', 'correct' => true],
                    ['text' => 'max-width is a fixed size; width allows the element to grow beyond the value', 'correct' => false],
                    ['text' => 'They are identical — both set an absolute width that cannot change', 'correct' => false],
                    ['text' => 'max-width applies only to images; width applies to all elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `min-height` do in CSS?',
                'explanation' => '`min-height` sets the minimum height an element must have, even if its content is shorter. The element can still grow taller if the content requires more space. This is useful for sections that should never collapse to zero height (e.g., a hero section or card that must always be at least 200 px tall, but expands with longer content).',
                'options'     => [
                    ['text' => 'Sets a minimum height the element must reach; the element can still grow taller with content', 'correct' => true],
                    ['text' => 'Fixes the element\'s height so it can never change regardless of content', 'correct' => false],
                    ['text' => 'Sets the height of the smallest child element inside the container', 'correct' => false],
                    ['text' => 'Prevents the element from being taller than its parent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `cursor: pointer` do in CSS?',
                'explanation' => '`cursor: pointer` changes the mouse cursor to a hand icon when hovering over the element, signalling to the user that the element is clickable. Browsers apply this automatically to `<a>` and `<button>` elements. Applying it to custom interactive elements (like `<div>` buttons or cards) improves usability by giving users the expected clickable affordance.',
                'options'     => [
                    ['text' => 'Changes the cursor to a hand icon, indicating the element is clickable', 'correct' => true],
                    ['text' => 'Makes the element respond to pointer events only, not touch', 'correct' => false],
                    ['text' => 'Captures cursor movement inside the element and prevents it from leaving', 'correct' => false],
                    ['text' => 'Sets the element\'s pointer-events to auto from none', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which CSS property changes the style of list item markers (bullets/numbers)?',
                'explanation' => '`list-style-type` sets the marker used before each list item. Common values: `disc` (filled circle, default for `<ul>`), `circle`, `square`, `decimal` (default for `<ol>`), `lower-alpha`, `lower-roman`, and `none` (removes the marker). `list-style` is the shorthand for `list-style-type`, `list-style-position`, and `list-style-image`.',
                'options'     => [
                    ['text' => 'list-style-type', 'correct' => true],
                    ['text' => 'list-marker', 'correct' => false],
                    ['text' => 'bullet-style', 'correct' => false],
                    ['text' => 'marker-type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `text-decoration: none` do?',
                'explanation' => '`text-decoration` controls underlines, overlines, and strikethroughs on text. Setting it to `none` removes all decorations — most commonly used to remove the default underline from `<a>` anchor elements. Other values include `underline`, `overline`, `line-through`, and `underline dotted`. The property also accepts colour and style sub-values.',
                'options'     => [
                    ['text' => 'Removes all text decorations such as underlines, overlines, and strikethroughs', 'correct' => true],
                    ['text' => 'Hides the text content but keeps the element visible in the layout', 'correct' => false],
                    ['text' => 'Prevents text from wrapping to a new line', 'correct' => false],
                    ['text' => 'Disables CSS transitions on text-related properties', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the CSS `line-height` property control?',
                'explanation' => '`line-height` controls the vertical space between lines of text (also called leading). A unitless value like `line-height: 1.5` multiplies the current font size, making it proportional and the recommended approach. Fixed values (`20px`) do not scale with font size. The default browser value is roughly 1.2. Increasing `line-height` improves readability for body text.',
                'options'     => [
                    ['text' => 'The vertical space between lines of text within an element', 'correct' => true],
                    ['text' => 'The height of the tallest line box in a text container', 'correct' => false],
                    ['text' => 'The number of text lines allowed before overflow is triggered', 'correct' => false],
                    ['text' => 'The distance between the element and its neighbouring elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `letter-spacing` CSS property do?',
                'explanation' => '`letter-spacing` adds or removes space between individual characters (glyphs). A positive value increases spacing; a negative value tightens it. It is often used for all-caps headings to improve legibility (`letter-spacing: 0.1em`) or for branding treatments. Unlike word spacing, it affects the space after every character, including spaces between words.',
                'options'     => [
                    ['text' => 'Adds or removes space between individual characters in text', 'correct' => true],
                    ['text' => 'Controls the spacing between words (spaces only, not characters)', 'correct' => false],
                    ['text' => 'Sets the vertical gap between two lines of text', 'correct' => false],
                    ['text' => 'Adjusts the horizontal padding inside an inline element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which CSS colour format uses hue, saturation, and lightness values?',
                'explanation' => 'HSL (Hue, Saturation, Lightness) is a human-friendly colour model. Hue is a degree on the colour wheel (0–360), saturation is a percentage (0% = grey, 100% = full colour), and lightness is a percentage (0% = black, 100% = white). Example: `hsl(210, 60%, 50%)`. It is easier to create colour palettes with HSL by adjusting just the lightness or saturation.',
                'options'     => [
                    ['text' => 'hsl() — e.g., hsl(210, 60%, 50%)', 'correct' => true],
                    ['text' => 'rgb() — e.g., rgb(210, 60, 50)', 'correct' => false],
                    ['text' => 'hex — e.g., #3399ff', 'correct' => false],
                    ['text' => 'cmyk() — e.g., cmyk(0.2, 0.4, 0.0, 0.2)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `opacity: 0.5` and `rgba(0,0,0,0.5)`?',
                'explanation' => '`opacity` applies transparency to the entire element and all its children — text, background, images, and child elements all become semi-transparent. `rgba()` applies transparency only to the specific property it is used on (e.g., just the background colour), leaving the text and children fully opaque. Use `rgba` on `background-color` when you want a translucent background but fully visible text.',
                'options'     => [
                    ['text' => 'opacity makes the entire element (including children) semi-transparent; rgba only affects the specific colour property', 'correct' => true],
                    ['text' => 'rgba applies to the whole element; opacity only affects the background colour', 'correct' => false],
                    ['text' => 'They are identical — both produce the same visual result and affect children equally', 'correct' => false],
                    ['text' => 'opacity is for greyscale; rgba is for colour transparency', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `display: inline-block` do?',
                'explanation' => '`display: inline-block` makes an element flow inline (like text and inline elements) while allowing `width`, `height`, `margin`, and `padding` to be set as if it were a block element. Pure inline elements (`<span>`) ignore width and height. Block elements start on new lines. `inline-block` is a middle ground used for buttons, badges, and navigation items.',
                'options'     => [
                    ['text' => 'Flows inline with text but respects width, height, margin, and padding like a block element', 'correct' => true],
                    ['text' => 'Makes the element invisible but keeps it in the inline flow', 'correct' => false],
                    ['text' => 'Converts the element to a block-level element that also acts as a flex container', 'correct' => false],
                    ['text' => 'Aligns the element inside a block container without changing its display type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `float` property do and how does `clear` relate to it?',
                'explanation' => '`float` removes an element from normal flow and pushes it to the left or right, allowing text and other inline elements to wrap around it. `clear: both` (or `left`/`right`) on a subsequent element prevents it from wrapping next to floated elements and forces it below them. Modern layouts favour Flexbox and Grid over floats, but floats remain relevant for text-wrapping around images.',
                'options'     => [
                    ['text' => 'float pulls an element to the left or right allowing content to wrap; clear prevents wrapping next to floats', 'correct' => true],
                    ['text' => 'float centres an element; clear removes all CSS from an element', 'correct' => false],
                    ['text' => 'float makes an element translucent; clear resets its opacity', 'correct' => false],
                    ['text' => 'float and clear are only for vertical alignment within table cells', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `overflow: auto` and `overflow: scroll`?',
                'explanation' => '`overflow: scroll` always shows scrollbars on the element, even if the content does not overflow — it reserves the scrollbar space regardless. `overflow: auto` only adds scrollbars when the content actually overflows. `auto` is preferred for most use cases because it avoids unnecessary scrollbar gutters when content fits within the container.',
                'options'     => [
                    ['text' => 'overflow: scroll always shows scrollbars; overflow: auto only adds them when content overflows', 'correct' => true],
                    ['text' => 'overflow: auto always shows scrollbars; overflow: scroll only adds them when needed', 'correct' => false],
                    ['text' => 'They are identical — both show scrollbars only when content overflows', 'correct' => false],
                    ['text' => 'overflow: scroll hides overflowing content; overflow: auto clips it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `position: fixed` and `position: sticky`?',
                'explanation' => '`position: fixed` positions the element relative to the viewport and it stays in place even when the page scrolls — it is completely removed from the document flow. `position: sticky` is a hybrid: the element scrolls with the page until it reaches a specified threshold (e.g., `top: 0`), then sticks in place within its parent container. Sticky stays inside the parent; fixed stays in the viewport.',
                'options'     => [
                    ['text' => 'fixed stays fixed relative to the viewport always; sticky sticks at a threshold within its parent', 'correct' => true],
                    ['text' => 'sticky stays fixed relative to the viewport; fixed sticks only within the parent', 'correct' => false],
                    ['text' => 'They are identical — both stick to the top of the viewport when scrolling', 'correct' => false],
                    ['text' => 'fixed requires a top/left value; sticky works without any offset properties', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do the `top`, `right`, `bottom`, and `left` CSS properties do?',
                'explanation' => '`top`, `right`, `bottom`, and `left` are offset properties that work only on positioned elements (`position` other than `static`). They move the element in the specified direction relative to its containing block. For `absolute`/`fixed`, they position the element within the positioned ancestor or viewport. For `relative`, they offset from the element\'s normal position without changing the flow.',
                'options'     => [
                    ['text' => 'Set the offset of a positioned element from its reference point (ancestor or viewport)', 'correct' => true],
                    ['text' => 'Set margin on individual sides and work on all elements regardless of position', 'correct' => false],
                    ['text' => 'Only work with display: flex to align items within the container', 'correct' => false],
                    ['text' => 'Define the border thickness on each side of the element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `vertical-align` do in CSS?',
                'explanation' => '`vertical-align` aligns inline or inline-block elements relative to their line box. Values include `top`, `middle`, `bottom`, `baseline` (default), `super`, `sub`, and length/percentage offsets. It does NOT work on block-level elements. Common use cases: vertically centering an icon next to text, or adjusting the alignment of images inside text lines.',
                'options'     => [
                    ['text' => 'Aligns inline or inline-block elements relative to the surrounding line of text', 'correct' => true],
                    ['text' => 'Vertically centres a block element inside its parent container', 'correct' => false],
                    ['text' => 'Works on flex items to control their cross-axis alignment', 'correct' => false],
                    ['text' => 'Sets the vertical margin between adjacent block elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `white-space: nowrap` do in CSS?',
                'explanation' => '`white-space: nowrap` prevents text from wrapping to the next line, forcing it all onto one line even if it overflows the container. This is commonly combined with `overflow: hidden` and `text-overflow: ellipsis` to truncate long text with an ellipsis. The default value `normal` wraps text at word boundaries. `pre` preserves whitespace and newlines.',
                'options'     => [
                    ['text' => 'Prevents text from wrapping, keeping it all on a single line', 'correct' => true],
                    ['text' => 'Removes all whitespace characters (spaces, tabs) from the text content', 'correct' => false],
                    ['text' => 'Forces the element to take up no more than one line height', 'correct' => false],
                    ['text' => 'Wraps text only at explicit line break tags, not at word boundaries', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            // --- original 10 ---
            [
                'question'    => 'What is the difference between `justify-content` and `align-items` in Flexbox?',
                'explanation' => 'In a flex container, `justify-content` aligns items along the main axis (horizontally in a `flex-direction: row`), and `align-items` aligns items along the cross axis (vertically in a row). Common values: `flex-start`, `flex-end`, `center`, `space-between`, `space-around`. The axes swap when `flex-direction: column`.',
                'options'     => [
                    ['text' => 'justify-content aligns on the main axis; align-items aligns on the cross axis', 'correct' => true],
                    ['text' => 'align-items aligns on the main axis; justify-content aligns on the cross axis', 'correct' => false],
                    ['text' => 'They are identical — just different names for the same property', 'correct' => false],
                    ['text' => 'justify-content is for multi-line; align-items is for single-line containers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are CSS custom properties (CSS variables)?',
                'explanation' => 'CSS custom properties are variables defined with `--` prefix and accessed with `var()`: `--primary: #4f46e5; color: var(--primary)`. They are scoped to the element where defined (and inherited by children). Defined on `:root`, they become global. Unlike preprocessor variables, they can be changed at runtime with JavaScript and respond to media queries.',
                'options'     => [
                    ['text' => 'Variables defined with -- prefix and used with var(), scoped to the element', 'correct' => true],
                    ['text' => 'CSS properties specific to the custom elements specification', 'correct' => false],
                    ['text' => 'A JavaScript API for modifying CSS at runtime', 'correct' => false],
                    ['text' => 'Properties added by the browser vendor that are not in the CSS standard', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a CSS media query?',
                'explanation' => 'Media queries apply CSS rules based on conditions like viewport width, screen resolution, or device type. Syntax: `@media (max-width: 768px) { ... }`. They are the foundation of responsive design — a single stylesheet can adapt its layout for phones, tablets, and desktops. Can also target dark mode: `@media (prefers-color-scheme: dark)`.',
                'options'     => [
                    ['text' => 'A conditional CSS block that applies styles based on viewport or device conditions', 'correct' => true],
                    ['text' => 'A JavaScript API for querying computed CSS values', 'correct' => false],
                    ['text' => 'A CSS function for querying values from external stylesheets', 'correct' => false],
                    ['text' => 'A way to import media files (images, videos) directly in CSS', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `flex: 1` mean in CSS?',
                'explanation' => '`flex: 1` is shorthand for `flex-grow: 1; flex-shrink: 1; flex-basis: 0%`. This means the element grows to fill available space proportionally and shrinks if needed. Multiple flex items with `flex: 1` share space equally. It is the most common way to create equal-width or fill-remaining-space columns in flex layouts.',
                'options'     => [
                    ['text' => 'Shorthand for flex-grow: 1, flex-shrink: 1, flex-basis: 0% — grows to fill available space', 'correct' => true],
                    ['text' => 'Sets the element to be the first child in a flex container', 'correct' => false],
                    ['text' => 'Enables flexbox on the element itself (same as display: flex)', 'correct' => false],
                    ['text' => 'Sets the flex order of the element to 1', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are CSS pseudo-elements (`::before` and `::after`)?',
                'explanation' => 'Pseudo-elements create virtual elements that are not in the HTML but can be styled as if they were. `::before` inserts a generated element as the first child, `::after` as the last child. They require a `content` property (even empty: `content: ""`). Used for decorative elements, tooltips, badges, and more — without adding markup.',
                'options'     => [
                    ['text' => 'Virtual elements inserted before/after an element\'s content that can be styled with CSS', 'correct' => true],
                    ['text' => 'JavaScript hooks that fire before and after CSS transitions', 'correct' => false],
                    ['text' => 'Selectors that target the element before and after user interaction', 'correct' => false],
                    ['text' => 'CSS comments that wrap around rule blocks', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the CSS `transition` property do?',
                'explanation' => '`transition` smoothly animates changes to CSS property values over time. Syntax: `transition: property duration timing-function delay`. Example: `transition: background-color 0.3s ease`. It triggers automatically when the specified property changes (e.g., on hover). Unlike animations, transitions go from state A to state B — they need a trigger.',
                'options'     => [
                    ['text' => 'Smoothly animates changes to CSS property values over a specified duration', 'correct' => true],
                    ['text' => 'Moves an element from one position to another on the page', 'correct' => false],
                    ['text' => 'Switches between two CSS stylesheets gradually', 'correct' => false],
                    ['text' => 'Controls how fast a page scrolls between sections', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How is CSS Grid different from Flexbox?',
                'explanation' => 'Flexbox is a 1D layout system — it works along either the main axis (row) or cross axis (column) at a time. CSS Grid is a 2D layout system that handles both rows and columns simultaneously. Flexbox is ideal for distributing items in a single direction (nav bars, card rows); Grid is ideal for complex page layouts with rows and columns aligned together.',
                'options'     => [
                    ['text' => 'Grid is 2D (rows and columns simultaneously); Flexbox is 1D (one axis at a time)', 'correct' => true],
                    ['text' => 'Flexbox is newer and replaces Grid entirely in modern browsers', 'correct' => false],
                    ['text' => 'Grid only works with fixed pixel sizes; Flexbox supports fluid layouts', 'correct' => false],
                    ['text' => 'They are interchangeable — both work identically for all layout needs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `overflow: hidden` do in CSS?',
                'explanation' => '`overflow: hidden` clips content that extends beyond the element\'s bounds — the overflow is not visible. It also creates a new Block Formatting Context (BFC), which is useful for containing floated children (clearfix) and preventing margin collapse. It prevents scroll on the element but note it also clips `box-shadow` and absolutely positioned children.',
                'options'     => [
                    ['text' => 'Clips content that exceeds the element\'s bounds and creates a new block formatting context', 'correct' => true],
                    ['text' => 'Hides the element when it overflows its parent', 'correct' => false],
                    ['text' => 'Makes overflowing content scroll automatically', 'correct' => false],
                    ['text' => 'Prevents child elements from being taller than the parent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is BEM in CSS?',
                'explanation' => 'BEM (Block–Element–Modifier) is a naming convention for CSS classes. Block: a standalone component (`.card`). Element: a part of a block (`.card__title`). Modifier: a variation of a block or element (`.card--featured`, `.card__title--large`). BEM prevents class name collisions and makes stylesheets more readable and maintainable in large projects.',
                'options'     => [
                    ['text' => 'Block–Element–Modifier: a naming convention that structures CSS class names for scalability', 'correct' => true],
                    ['text' => 'Browser Extension Mechanism: a way to add custom CSS without changing source files', 'correct' => false],
                    ['text' => 'Basic Element Markup: the minimum required CSS properties for styling an element', 'correct' => false],
                    ['text' => 'Border-Edge-Margin: an alternative name for the CSS Box Model', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `calc()` function do in CSS?',
                'explanation' => '`calc()` performs mathematical calculations in CSS, mixing different units: `width: calc(100% - 2rem)`. It supports addition (+), subtraction (-), multiplication (*), and division (/). Crucially, operands around + and - must have spaces around the operators. It enables dynamic calculations that preprocessors cannot achieve because `calc()` is evaluated at render time.',
                'options'     => [
                    ['text' => 'Performs math in CSS, mixing different units: width: calc(100% - 2rem)', 'correct' => true],
                    ['text' => 'A JavaScript function for calculating CSS values from TypeScript', 'correct' => false],
                    ['text' => 'Calculates the total number of CSS rules applied to an element', 'correct' => false],
                    ['text' => 'Computes and caches CSS values to improve rendering performance', 'correct' => false],
                ],
            ],
            // --- 23 additions ---
            [
                'question'    => 'What does `flex-direction: column` do to a flex container?',
                'explanation' => '`flex-direction` sets the main axis of a flex container. With the default `row`, items are placed horizontally left to right. With `column`, items are stacked vertically top to bottom. `row-reverse` and `column-reverse` reverse the direction. When you change `flex-direction`, the main and cross axes swap, so `justify-content` becomes vertical and `align-items` becomes horizontal.',
                'options'     => [
                    ['text' => 'Changes the main axis to vertical, stacking flex items from top to bottom', 'correct' => true],
                    ['text' => 'Makes flex items wrap into multiple columns automatically', 'correct' => false],
                    ['text' => 'Converts the flex container into a CSS Grid column layout', 'correct' => false],
                    ['text' => 'Aligns the flex container itself to the top of its parent column', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `flex-wrap: wrap` do?',
                'explanation' => '`flex-wrap` controls whether flex items are forced onto one line or can wrap onto multiple lines. The default `nowrap` squishes all items onto one line (they may overflow). `wrap` allows items to wrap to the next line when they run out of space. `wrap-reverse` wraps in the opposite direction. Wrapping is essential when the total item width exceeds the container.',
                'options'     => [
                    ['text' => 'Allows flex items to wrap onto multiple lines when they exceed the container width', 'correct' => true],
                    ['text' => 'Wraps text content inside each flex item automatically', 'correct' => false],
                    ['text' => 'Prevents flex items from exceeding their flex-basis size', 'correct' => false],
                    ['text' => 'Applies word-wrap to all text inside the flex container', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `align-content` do in a flex container and how does it differ from `align-items`?',
                'explanation' => '`align-content` controls spacing between multiple flex lines (rows) on the cross axis — it only has effect when `flex-wrap: wrap` creates more than one line. `align-items` aligns items within a single flex line. If there is only one line, `align-content` has no effect. Values like `space-between` distribute the lines evenly with space between them.',
                'options'     => [
                    ['text' => 'align-content spaces multiple flex lines; align-items aligns items within a single line', 'correct' => true],
                    ['text' => 'align-content aligns all items vertically; align-items aligns them horizontally', 'correct' => false],
                    ['text' => 'They are identical — align-content is just a newer alias for align-items', 'correct' => false],
                    ['text' => 'align-content works on flex items; align-items works on the flex container', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `order` property do on a flex item?',
                'explanation' => '`order` controls the visual rendering order of flex items without changing the HTML source order. Items are sorted from lowest to highest `order` value. The default is `0` for all items. This is useful for reordering elements for different layouts (e.g., moving a sidebar before main content on mobile) without touching the HTML.',
                'options'     => [
                    ['text' => 'Changes the visual rendering order of the flex item without altering the HTML source order', 'correct' => true],
                    ['text' => 'Sets the item\'s numeric position in an ordered list', 'correct' => false],
                    ['text' => 'Determines how many items come before this item in the DOM tree', 'correct' => false],
                    ['text' => 'Sets the z-index stacking order for overlapping flex items', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `flex-grow`, `flex-shrink`, and `flex-basis`?',
                'explanation' => '`flex-basis` sets the initial size of a flex item before free space is distributed. `flex-grow` defines how much an item grows relative to others when there is extra space (0 = no growth). `flex-shrink` defines how much an item shrinks relative to others when there is insufficient space (0 = no shrink). Together as `flex: grow shrink basis`, e.g., `flex: 1 1 0%`.',
                'options'     => [
                    ['text' => 'flex-basis sets initial size; flex-grow controls growth when space is available; flex-shrink controls shrinking when space is tight', 'correct' => true],
                    ['text' => 'flex-grow sets initial size; flex-basis controls growth; flex-shrink sets the minimum size', 'correct' => false],
                    ['text' => 'All three are identical — they are different ways to set the same flex size property', 'correct' => false],
                    ['text' => 'flex-shrink is only relevant for fixed-width containers; flex-grow works in any container', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you define explicit row sizes in CSS Grid?',
                'explanation' => '`grid-template-rows` defines the number and size of explicit row tracks in a grid. Example: `grid-template-rows: 80px 1fr auto` creates three rows — a fixed 80 px header, a flexible middle row, and an auto-sized bottom row. The `fr` unit distributes remaining space proportionally. Without defining rows, grid auto-creates them with `grid-auto-rows`.',
                'options'     => [
                    ['text' => 'Use grid-template-rows to define row track sizes, e.g., grid-template-rows: 80px 1fr auto', 'correct' => true],
                    ['text' => 'Use row-size property on each grid item individually', 'correct' => false],
                    ['text' => 'Use grid-row-definition: 3 to create three equal rows', 'correct' => false],
                    ['text' => 'Rows cannot be explicitly sized in CSS Grid; only columns can', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `grid-column: 1 / 3` mean on a grid item?',
                'explanation' => '`grid-column: 1 / 3` is shorthand for `grid-column-start: 1; grid-column-end: 3`. The item starts at column line 1 and ends at column line 3, spanning two column tracks. Negative values count from the end: `1 / -1` spans the full width. The keyword `span` can also be used: `grid-column: 2 / span 2` starts at line 2 and spans 2 tracks.',
                'options'     => [
                    ['text' => 'The item starts at column line 1 and ends at column line 3, spanning two columns', 'correct' => true],
                    ['text' => 'The item is placed in columns 1 and 3, skipping column 2', 'correct' => false],
                    ['text' => 'The item occupies a 1-by-3 grid area starting from the first column', 'correct' => false],
                    ['text' => 'The item spans from column 1 to column 3 of the parent element, not the grid', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `grid-auto-flow: dense` do in CSS Grid?',
                'explanation' => '`grid-auto-flow` controls how the auto-placement algorithm places grid items. `row` (default) fills each row left to right before moving to the next. `column` fills columns first. `dense` additionally back-fills gaps with smaller items that fit — useful for masonry-like layouts. Without `dense`, the grid leaves holes to preserve source order.',
                'options'     => [
                    ['text' => 'Tells the auto-placement algorithm to back-fill gaps with smaller items that fit, reducing holes', 'correct' => true],
                    ['text' => 'Forces all grid items to have the same density (uniform size)', 'correct' => false],
                    ['text' => 'Makes grid items fill their tracks completely by stretching them', 'correct' => false],
                    ['text' => 'Stacks all grid items in a single dense column', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `gap` and the old `grid-gap` property in CSS Grid?',
                'explanation' => '`grid-gap` was the original property for setting gutters between grid tracks, later renamed to `gap`. `gap` also works in Flexbox for spacing between flex items. The longhand properties are `row-gap` and `column-gap`. Modern code should use `gap` — `grid-gap` is still supported but considered a legacy alias. `gap` does not add space at the outer edges of the grid.',
                'options'     => [
                    ['text' => 'gap is the modern renamed version of grid-gap; both set gutters between tracks and gap also works in Flexbox', 'correct' => true],
                    ['text' => 'grid-gap applies to rows only; gap applies to both rows and columns', 'correct' => false],
                    ['text' => 'gap adds space outside the grid edges; grid-gap only between tracks', 'correct' => false],
                    ['text' => 'They are entirely different — grid-gap is for padding; gap is for margins', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `minmax()` function do in CSS Grid?',
                'explanation' => '`minmax(min, max)` defines a size range for a grid track: the track is at least `min` but grows up to `max`. Example: `grid-template-columns: minmax(200px, 1fr)` creates a column that is at least 200 px wide but expands to fill available space. Combining `minmax(0, 1fr)` with `repeat()` and `auto-fill` creates fully responsive grids without media queries.',
                'options'     => [
                    ['text' => 'Defines a size range for a grid track — at least min, up to max', 'correct' => true],
                    ['text' => 'Returns the minimum of two values, used in grid calculations', 'correct' => false],
                    ['text' => 'Sets the minimum and maximum number of items per grid row', 'correct' => false],
                    ['text' => 'A math function for clamping a value between two numbers in any CSS property', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `repeat(auto-fill, minmax(200px, 1fr))` do in CSS Grid?',
                'explanation' => '`repeat(auto-fill, minmax(200px, 1fr))` creates as many columns as fit in the container with each column being at least 200 px wide and expanding equally to fill remaining space. `auto-fill` creates empty tracks for any remaining space; `auto-fit` collapses empty tracks so existing items stretch further. This pattern creates fully responsive grids without any media queries.',
                'options'     => [
                    ['text' => 'Creates as many 200px+ columns as fit in the container — a responsive grid without media queries', 'correct' => true],
                    ['text' => 'Repeats a fixed 200-pixel column exactly as many times as specified', 'correct' => false],
                    ['text' => 'Fills the grid with items that are auto-sized between 200px and 1fr', 'correct' => false],
                    ['text' => 'Creates a single column that auto-fills content between 200px and 100% width', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the CSS transition timing functions and what do they control?',
                'explanation' => 'Timing functions control the pace of a transition over its duration. `ease` (default) starts slow, speeds up, then slows at the end. `linear` moves at constant speed. `ease-in` starts slow then speeds up. `ease-out` starts fast then slows. `ease-in-out` combines both. `cubic-bezier(x1,y1,x2,y2)` allows custom curves. `steps(n)` creates stepped (non-smooth) transitions.',
                'options'     => [
                    ['text' => 'They control the pace/curve of the transition over time (ease, linear, ease-in-out, cubic-bezier, steps)', 'correct' => true],
                    ['text' => 'They set the total duration of the transition in seconds or milliseconds', 'correct' => false],
                    ['text' => 'They control which CSS properties are included in the transition', 'correct' => false],
                    ['text' => 'They determine the frame rate (fps) at which the transition renders', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `animation-fill-mode: forwards` do?',
                'explanation' => '`animation-fill-mode` controls what styles are applied before and after the animation runs. `forwards` keeps the final keyframe\'s styles applied after the animation ends — without it, the element snaps back to its original styles. `backwards` applies the first keyframe\'s styles during the animation delay. `both` applies `forwards` and `backwards` together.',
                'options'     => [
                    ['text' => 'Keeps the final keyframe styles applied after the animation completes, preventing snap-back', 'correct' => true],
                    ['text' => 'Makes the animation play forward instead of in reverse', 'correct' => false],
                    ['text' => 'Moves the element forward in z-index after the animation finishes', 'correct' => false],
                    ['text' => 'Fills the animation duration with extra time at the end before it resets', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `animation-direction: alternate` do?',
                'explanation' => '`animation-direction` controls whether the animation plays forward, backward, or alternates. `normal` (default) plays from 0% to 100% each iteration. `reverse` plays from 100% to 0% each iteration. `alternate` plays forward on odd iterations and backward on even iterations, creating a ping-pong effect. `alternate-reverse` does the opposite.',
                'options'     => [
                    ['text' => 'Makes the animation play forward on odd iterations and backward on even iterations (ping-pong)', 'correct' => true],
                    ['text' => 'Alternates the animation between two named @keyframes definitions', 'correct' => false],
                    ['text' => 'Randomly reverses the animation direction on each iteration', 'correct' => false],
                    ['text' => 'Plays the animation in the direction opposite to the text writing mode', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What CSS property applies 2D transforms like translate, scale, rotate, and skew?',
                'explanation' => 'The `transform` property applies visual transformations without affecting document flow. `translate(x, y)` moves the element. `scale(x, y)` resizes it. `rotate(angle)` spins it. `skew(xAngle, yAngle)` distorts it along the axes. Multiple transforms can be chained: `transform: rotate(45deg) scale(1.2)`. Transforms are GPU-accelerated and do not trigger layout reflows.',
                'options'     => [
                    ['text' => 'The transform property — e.g., transform: translate(50px, 0) scale(1.2) rotate(45deg)', 'correct' => true],
                    ['text' => 'The movement property with translate, scale, rotate, and skew sub-properties', 'correct' => false],
                    ['text' => 'The position property with transform values instead of top/left offsets', 'correct' => false],
                    ['text' => 'The animation property with a keyframes block defining transform states', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `transform-origin` control?',
                'explanation' => '`transform-origin` sets the origin point for CSS transforms. By default it is `50% 50%` (the element\'s centre). Changing it to `top left` (0% 0%) makes rotations spin from the top-left corner instead of the centre. It accepts keywords (`top`, `bottom`, `left`, `right`, `center`), percentages, and lengths. It affects `rotate`, `scale`, and `skew` transforms.',
                'options'     => [
                    ['text' => 'Sets the reference point (pivot) around which CSS transforms like rotate and scale are applied', 'correct' => true],
                    ['text' => 'Sets the origin coordinate system for CSS Grid placement', 'correct' => false],
                    ['text' => 'Defines the point from which an element\'s position: absolute offsets are calculated', 'correct' => false],
                    ['text' => 'Controls the starting point for CSS gradient directions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you apply a 3D perspective effect in CSS?',
                'explanation' => '3D transforms require a `perspective` value to define the depth of the 3D space. Applied to the parent: `perspective: 800px` — lower values create a more extreme 3D effect. The child can then use `rotateX()`, `rotateY()`, or `rotateZ()`. `transform-style: preserve-3d` on the parent ensures child elements exist in the same 3D space.',
                'options'     => [
                    ['text' => 'Add perspective: 800px on the parent and use rotateX/rotateY transforms on the child', 'correct' => true],
                    ['text' => 'Use transform: perspective(800px) on the element itself and add z-index', 'correct' => false],
                    ['text' => '3D transforms require JavaScript — CSS alone cannot create perspective effects', 'correct' => false],
                    ['text' => 'Set position: 3d on the container and use left/top/depth offsets on children', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are CSS counters and how are they used?',
                'explanation' => 'CSS counters are variables maintained by CSS and incremented by CSS rules. `counter-reset: section` initialises the counter on an element. `counter-increment: section` increments it on each target. `content: counter(section)` in a `::before` pseudo-element displays the value. They are used for automatic numbering of sections, figures, or list items without JavaScript.',
                'options'     => [
                    ['text' => 'CSS-maintained variables that auto-number elements using counter-reset, counter-increment, and counter()', 'correct' => true],
                    ['text' => 'A JavaScript API that counts DOM elements and exposes the value to CSS', 'correct' => false],
                    ['text' => 'Browser performance metrics exposed as CSS custom properties', 'correct' => false],
                    ['text' => 'A CSS function that counts the number of children inside a container', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `@font-face` do in CSS?',
                'explanation' => '`@font-face` allows you to define a custom font for use in your stylesheet by providing a font name and a source URL. The browser downloads the font file (woff2, woff, ttf, etc.) and makes it available via the `font-family` property. You can define multiple `@font-face` blocks for different weights/styles of the same font family.',
                'options'     => [
                    ['text' => 'Loads a custom font from a URL and makes it available via font-family', 'correct' => true],
                    ['text' => 'Sets the default font face for all elements on the page', 'correct' => false],
                    ['text' => 'Embeds a font icon library (like Font Awesome) into the CSS', 'correct' => false],
                    ['text' => 'Defines the typographic baseline grid for vertical rhythm', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `font-display: swap` do in a `@font-face` rule?',
                'explanation' => '`font-display` controls how a font is displayed while it is loading. `swap` shows a fallback system font immediately (no invisible text) and swaps to the custom font once it loads — improving perceived performance. `block` hides text briefly (FOIT — Flash of Invisible Text). `optional` only uses the font if it loads fast enough; otherwise uses the fallback permanently.',
                'options'     => [
                    ['text' => 'Shows a fallback font immediately while the custom font loads, then swaps — eliminating invisible text', 'correct' => true],
                    ['text' => 'Swaps between two custom fonts at random for A/B testing', 'correct' => false],
                    ['text' => 'Disables font loading entirely and uses only system fonts', 'correct' => false],
                    ['text' => 'Caches the font on first load and swaps the cache on return visits', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `mix-blend-mode` do in CSS?',
                'explanation' => '`mix-blend-mode` controls how an element\'s content blends with the content behind it, similar to layer blending modes in Photoshop. Values include `multiply`, `screen`, `overlay`, `darken`, `lighten`, `color-burn`, `difference`, `luminosity`, and more. `multiply` darkens where colours overlap; `screen` lightens. This enables creative visual effects without image editing.',
                'options'     => [
                    ['text' => 'Controls how an element blends with the content rendered behind it (multiply, screen, overlay, etc.)', 'correct' => true],
                    ['text' => 'Mixes two CSS animations and plays them simultaneously on one element', 'correct' => false],
                    ['text' => 'Blends the element\'s background and foreground colours together', 'correct' => false],
                    ['text' => 'Sets how many CSS blend layers can be active on the page at once', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `isolation: isolate` CSS property do?',
                'explanation' => '`isolation: isolate` creates a new stacking context for an element, preventing its `mix-blend-mode` children from blending with content outside the element. Without isolation, a child with `mix-blend-mode: multiply` would blend with everything behind it in the viewport. `isolate` confines the blending to within the element\'s subtree.',
                'options'     => [
                    ['text' => 'Creates a new stacking context that confines mix-blend-mode effects to within the element', 'correct' => true],
                    ['text' => 'Prevents CSS from being inherited by child elements', 'correct' => false],
                    ['text' => 'Isolates the element\'s rendering into a separate GPU layer for performance', 'correct' => false],
                    ['text' => 'Prevents JavaScript from accessing or modifying the element\'s styles', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            // --- original 10 ---
            [
                'question'    => 'How is CSS specificity calculated?',
                'explanation' => 'Specificity is calculated as a three-part value (A, B, C): A = number of ID selectors, B = number of class/pseudo-class/attribute selectors, C = number of type/pseudo-element selectors. Inline styles override everything (weight 1,0,0,0). The `!important` declaration overrides normal specificity entirely but creates maintenance problems. Higher A beats lower A, then B compared, then C.',
                'options'     => [
                    ['text' => 'Inline > ID (100) > Class/pseudo-class (10) > Element (1) — compared left-to-right', 'correct' => true],
                    ['text' => 'The last rule in the file always wins regardless of selectors used', 'correct' => false],
                    ['text' => 'Specificity is calculated by counting all characters in the selector string', 'correct' => false],
                    ['text' => 'External stylesheets always have lower specificity than internal styles', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a CSS stacking context?',
                'explanation' => 'A stacking context is a three-dimensional concept where elements in the same context compete with each other for z-axis ordering. New stacking contexts are created by elements with `position` + `z-index`, `opacity < 1`, `transform`, `filter`, `will-change`, and others. Elements from different stacking contexts are compared as a whole — a child cannot appear above a sibling\'s parent even with a high z-index.',
                'options'     => [
                    ['text' => 'An isolated 3D context where z-index applies; created by position/z-index, opacity, transform, etc.', 'correct' => true],
                    ['text' => 'The order in which CSS stylesheets are loaded by the browser', 'correct' => false],
                    ['text' => 'A CSS feature that groups elements by stack order for animation purposes', 'correct' => false],
                    ['text' => 'The rendering sequence for block elements in normal document flow', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are CSS `@keyframes` and how are they used?',
                'explanation' => '`@keyframes` defines the states of an animation at specific points in time using percentages (`0%`, `50%`, `100%`) or keywords (`from`, `to`). The animation is applied to an element with the `animation` shorthand: `animation: name duration timing-function delay iteration-count direction`. Unlike transitions, animations can loop, run in reverse, and run without a trigger.',
                'options'     => [
                    ['text' => 'Define animation states at percentage milestones; applied to elements with the animation property', 'correct' => true],
                    ['text' => 'A CSS hook called by JavaScript to execute animation logic at specific frames', 'correct' => false],
                    ['text' => 'Define layout breakpoints similar to media queries', 'correct' => false],
                    ['text' => 'A preprocessor feature for reusable CSS blocks (mixins)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `will-change` CSS property?',
                'explanation' => '`will-change` hints to the browser that an element will be animated or changed, so the browser can set up hardware acceleration and create compositing layers in advance: `will-change: transform`. This can improve animation performance but has costs — it uses GPU memory, creates new stacking contexts, and should only be applied when needed and removed after.',
                'options'     => [
                    ['text' => 'Hints to the browser to optimise for upcoming changes, enabling hardware acceleration', 'correct' => true],
                    ['text' => 'Declares which CSS properties will change in a transition', 'correct' => false],
                    ['text' => 'Prevents changes to an element\'s CSS until a JavaScript condition is met', 'correct' => false],
                    ['text' => 'Marks a CSS property as experimental and subject to change', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `object-fit` in CSS?',
                'explanation' => '`object-fit` controls how replaced content (images, videos) fits within its container. `cover` scales the image to fill the container while maintaining aspect ratio (may crop). `contain` scales to fit entirely within the container (may letterbox). `fill` stretches to fill (ignores aspect ratio). `none` keeps the original size. Essential for image galleries and media layouts.',
                'options'     => [
                    ['text' => 'Controls how an image or video is scaled to fit its container while maintaining aspect ratio', 'correct' => true],
                    ['text' => 'Defines how objects (HTML elements) are positioned relative to each other', 'correct' => false],
                    ['text' => 'Sets how a flex item fits within a flex container', 'correct' => false],
                    ['text' => 'Controls how JavaScript objects are serialised to CSS strings', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a CSS container query?',
                'explanation' => 'Container queries (`@container`) apply styles based on the size of a containing element rather than the viewport. This makes components truly self-contained and reusable — a card component can rearrange itself when its container is narrow, regardless of the viewport width. The container must be declared with `container-type: inline-size`.',
                'options'     => [
                    ['text' => 'Applies styles based on the size of a parent container, not the viewport', 'correct' => true],
                    ['text' => 'Queries CSS custom properties defined on container elements', 'correct' => false],
                    ['text' => 'An alternative name for a media query targeting a specific HTML container', 'correct' => false],
                    ['text' => 'A JavaScript API for reading an element\'s computed styles at runtime', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are CSS logical properties?',
                'explanation' => 'Logical properties map layout to writing directions instead of physical sides. Instead of `margin-left`, use `margin-inline-start` — in left-to-right mode this is left, in right-to-left mode it is right. `inline` refers to the writing direction (horizontal in LTR), `block` refers to the perpendicular direction (vertical in LTR). Essential for internationalized, multi-directional layouts.',
                'options'     => [
                    ['text' => 'Writing-mode-aware properties like margin-inline-start that adapt to text direction (LTR/RTL)', 'correct' => true],
                    ['text' => 'CSS properties that use logic operators (AND, OR, NOT) for conditional styling', 'correct' => false],
                    ['text' => 'Properties that only apply when a JavaScript condition is true', 'correct' => false],
                    ['text' => 'CSS variables with computed values based on mathematical expressions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `clip-path` CSS property?',
                'explanation' => '`clip-path` clips an element to a specific shape, hiding the portions outside. Shapes include `circle()`, `ellipse()`, `polygon()`, `inset()`, and `path()` (SVG paths). Example: `clip-path: circle(50%)` makes an element circular. Unlike `border-radius`, clip-path can create complex non-rectangular shapes and can be animated for creative effects.',
                'options'     => [
                    ['text' => 'Clips an element to a defined shape, hiding content outside the shape boundary', 'correct' => true],
                    ['text' => 'Copies a path from one element and applies it to another', 'correct' => false],
                    ['text' => 'Cuts and pastes a portion of one element onto another in the DOM', 'correct' => false],
                    ['text' => 'Defines the clickable area of an element for pointer events', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is CSS subgrid?',
                'explanation' => 'Subgrid allows a grid item\'s children to participate in the parent grid\'s row/column tracks instead of creating their own independent grid. With `grid-template-columns: subgrid`, the children align to the outer grid\'s columns. This solves the classic problem of aligning content across different card components that each have their own independent grids.',
                'options'     => [
                    ['text' => 'Allows grid item children to align to the parent grid\'s tracks instead of creating their own', 'correct' => true],
                    ['text' => 'A grid within a grid — a nested display: grid inside a grid item', 'correct' => false],
                    ['text' => 'A CSS property for creating smaller grid units inside each grid cell', 'correct' => false],
                    ['text' => 'An alias for grid-template with fewer required properties', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the CSS `contain` property used for?',
                'explanation' => '`contain` limits the scope of browser rendering work to a subtree of the DOM. Values include `layout` (element\'s layout does not affect the rest), `paint` (descendants do not display outside), `size` (element\'s size is independent of its children), and `content` (combines layout + paint). It is a performance optimization that tells the browser it can skip recalculating layout/paint for the rest of the page when this element changes.',
                'options'     => [
                    ['text' => 'Limits browser rendering scope to a subtree, enabling layout/paint optimisations', 'correct' => true],
                    ['text' => 'Wraps overflow content so it is contained within the parent', 'correct' => false],
                    ['text' => 'An alternative to overflow: hidden for clipping child elements', 'correct' => false],
                    ['text' => 'Prevents JavaScript from modifying an element\'s CSS', 'correct' => false],
                ],
            ],
            // --- 23 additions ---
            [
                'question'    => 'What is the CSS Houdini Paint API?',
                'explanation' => 'The CSS Houdini Paint API allows developers to write JavaScript worklets that generate custom CSS images (backgrounds, borders, masks) by painting directly to a canvas-like surface. Registered with `CSS.paintWorklet.addModule()` and used as `background: paint(my-painter)`, it lets you create complex, parameterised visual effects that are impossible with standard CSS properties alone.',
                'options'     => [
                    ['text' => 'A Houdini API for writing JavaScript worklets that paint custom CSS images (backgrounds, borders)', 'correct' => true],
                    ['text' => 'A CSS property that paints all child elements using the GPU rasteriser', 'correct' => false],
                    ['text' => 'An API for detecting and painting over broken CSS rules in the browser devtools', 'correct' => false],
                    ['text' => 'A canvas-based rendering mode that replaces CSS styling with pixel painting', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the CSS Typed Object Model (Typed OM)?',
                'explanation' => 'The CSS Typed OM (`element.attributeStyleMap`) replaces string-based style manipulation with a typed JavaScript API. Instead of `el.style.width = "10px"` (string), you use `el.attributeStyleMap.set("width", CSS.px(10))`. Values are returned as typed objects (`CSSUnitValue`, `CSSMathSum`, etc.), eliminating string parsing, reducing bugs, and improving performance.',
                'options'     => [
                    ['text' => 'A typed JavaScript API for CSS properties using attributeStyleMap, eliminating string parsing', 'correct' => true],
                    ['text' => 'A Document Object Model extension that exposes CSS types as HTML attributes', 'correct' => false],
                    ['text' => 'A TypeScript library for writing type-safe CSS-in-JS stylesheets', 'correct' => false],
                    ['text' => 'A browser tool that validates CSS property types at parse time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do CSS `@layer` cascade layers do?',
                'explanation' => '`@layer` (cascade layers) lets you explicitly control the specificity order of CSS rule groups. Layers declared later have higher priority than earlier layers, regardless of selector specificity. Unlayered styles always win over layered styles. This solves the problem of third-party library styles overriding your own — put the library in a lower layer and your styles in a higher one.',
                'options'     => [
                    ['text' => 'Define ordered priority groups for CSS rules; later layers win over earlier ones regardless of specificity', 'correct' => true],
                    ['text' => 'Split a CSS file into lazy-loaded layers for performance optimisation', 'correct' => false],
                    ['text' => 'Create z-index layers in CSS without needing the position property', 'correct' => false],
                    ['text' => 'Group CSS rules by component layer (atoms, molecules, organisms)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `:has()` CSS pseudo-class do?',
                'explanation' => '`:has()` is the "relational" pseudo-class — it selects a parent element based on whether it contains a specific descendant. Example: `form:has(input:invalid)` styles a form that has at least one invalid input. It is the long-awaited "parent selector" in CSS. It can also be combined forward: `h2:has(+ p)` styles an `h2` immediately followed by a `p`.',
                'options'     => [
                    ['text' => 'Selects an element if it contains a matching descendant — the CSS parent selector', 'correct' => true],
                    ['text' => 'Checks whether a CSS property has a value assigned to it', 'correct' => false],
                    ['text' => 'Selects elements that have a specific HTML attribute set', 'correct' => false],
                    ['text' => 'A JavaScript-only feature that cannot be used in pure CSS stylesheets', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `:is()` and `:where()` in CSS?',
                'explanation' => '`:is()` and `:where()` both accept a selector list and match any element that matches one of the selectors. The key difference is specificity: `:is()` takes the specificity of its most specific argument, while `:where()` always has zero specificity. Use `:where()` for reusable base styles that should be easy to override; use `:is()` for convenience without losing specificity.',
                'options'     => [
                    ['text' => ':is() takes the specificity of its highest argument; :where() always has zero specificity', 'correct' => true],
                    ['text' => ':where() takes the specificity of its highest argument; :is() always has zero specificity', 'correct' => false],
                    ['text' => 'They are identical — both have zero specificity and accept selector lists', 'correct' => false],
                    ['text' => ':is() matches the first selector in the list; :where() matches any selector in the list', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does `:not()` work with complex selectors in modern CSS?',
                'explanation' => 'Modern CSS allows `:not()` to accept complex selector lists, not just simple selectors. Example: `li:not(.active, .disabled)` excludes elements matching either class. `:not(.parent .child)` is also valid. Specificity is determined by the most specific argument inside `:not()`. Earlier CSS versions only allowed a single simple selector inside `:not()`.',
                'options'     => [
                    ['text' => 'Modern :not() accepts a comma-separated list of complex selectors; specificity is from the highest argument', 'correct' => true],
                    ['text' => ':not() can only accept a single class or element selector — lists are invalid', 'correct' => false],
                    ['text' => ':not() has zero specificity regardless of what is inside it', 'correct' => false],
                    ['text' => ':not() with multiple selectors requires the :is() wrapper to work correctly', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `:nth-child(2n+1)` match in CSS?',
                'explanation' => '`:nth-child(An+B)` uses a formula where `n` is a non-negative integer starting at 0. `A` is the cycle size and `B` is the offset. `2n+1` matches elements at positions 1, 3, 5, 7… (all odd positions). It is equivalent to `:nth-child(odd)`. `2n` matches even positions. `3n+2` matches positions 2, 5, 8… The formula gives very flexible repeating pattern selection.',
                'options'     => [
                    ['text' => 'Every odd-positioned element (1st, 3rd, 5th…) — equivalent to :nth-child(odd)', 'correct' => true],
                    ['text' => 'Every even-positioned element (2nd, 4th, 6th…)', 'correct' => false],
                    ['text' => 'Only the element at position 3 (the literal value 2n+1 = 3)', 'correct' => false],
                    ['text' => 'The second element that is also a first child of its parent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `::slotted()` CSS pseudo-element target?',
                'explanation' => '`::slotted()` targets elements that have been slotted into a Web Component\'s shadow DOM `<slot>`. It is used inside the shadow root\'s stylesheet to style light DOM children that are distributed into the component\'s slots. Only elements at the top level of the slotted content can be styled — deeply nested slotted descendants cannot be targeted.',
                'options'     => [
                    ['text' => 'Targets light DOM elements projected into a Web Component shadow DOM slot', 'correct' => true],
                    ['text' => 'Targets the slot element itself inside a shadow DOM template', 'correct' => false],
                    ['text' => 'Selects elements with a slot HTML attribute on any element', 'correct' => false],
                    ['text' => 'A pseudo-element for inserting content into named grid areas', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `::part()` CSS pseudo-element do?',
                'explanation' => '`::part()` allows external CSS to style specific parts of a Web Component\'s shadow DOM that the component author has explicitly exposed via the `part` HTML attribute. Example: `my-button::part(inner)` styles the element with `part="inner"` inside `<my-button>`\'s shadow tree. It is the intentional styling hook for Web Component APIs, unlike `::slotted()` which targets projected content.',
                'options'     => [
                    ['text' => 'Allows external CSS to style named parts inside a Web Component\'s shadow DOM', 'correct' => true],
                    ['text' => 'Selects a partial match of an element\'s class or attribute value', 'correct' => false],
                    ['text' => 'Splits an element into multiple individually styled parts', 'correct' => false],
                    ['text' => 'Targets the last part of a compound selector for specificity calculation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `env()` CSS function do?',
                'explanation' => '`env()` reads environment variables provided by the browser or operating system, most commonly the safe area insets on notched mobile devices: `padding-top: env(safe-area-inset-top)`. This prevents content from being hidden behind notches or home indicators on iOS/Android. Unlike `var()` which reads custom properties, `env()` reads browser-level environment values.',
                'options'     => [
                    ['text' => 'Reads browser environment variables like safe-area-inset-top for notch-aware layouts', 'correct' => true],
                    ['text' => 'Reads operating system environment variables like PATH into CSS values', 'correct' => false],
                    ['text' => 'Returns the current CSS environment name (development, production, etc.)', 'correct' => false],
                    ['text' => 'Accesses environment-specific CSS custom properties defined in JavaScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `@property` rule do in CSS?',
                'explanation' => '`@property` (part of CSS Houdini) registers a custom property with a type, initial value, and inheritance behaviour. Example: `@property --hue { syntax: "<number>"; inherits: false; initial-value: 0; }`. This enables the browser to animate custom properties correctly — without it, the browser treats custom properties as strings and cannot interpolate between values for transitions/animations.',
                'options'     => [
                    ['text' => 'Registers a typed custom property with an initial value, enabling custom property animation', 'correct' => true],
                    ['text' => 'Declares a standard CSS property as deprecated and schedules its removal', 'correct' => false],
                    ['text' => 'Creates a CSS property alias that maps one property name to another', 'correct' => false],
                    ['text' => 'Defines a read-only CSS property that cannot be overridden by descendant rules', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does specificity work across `@layer` cascade layers?',
                'explanation' => 'Within the same `@layer`, normal specificity rules apply. Across layers, later-declared layers win over earlier ones regardless of selector specificity — a `*` universal selector in a later layer beats an ID selector in an earlier layer. Unlayered styles sit above all layered styles in priority. `!important` reverses the layer order for important declarations.',
                'options'     => [
                    ['text' => 'Later layers win over earlier layers regardless of selector specificity within those layers', 'correct' => true],
                    ['text' => 'Specificity is calculated across all layers combined as if layers did not exist', 'correct' => false],
                    ['text' => 'Each layer has its own isolated specificity — the highest specificity across layers wins', 'correct' => false],
                    ['text' => 'Layers have no effect on specificity; they only change rule source order', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the CSS `@scope` rule do?',
                'explanation' => '`@scope` limits CSS rules to a specific subtree of the DOM, optionally with a lower boundary (a "donut" scope). Example: `@scope (.card) to (.card-footer) { p { color: red; } }` styles `p` elements inside `.card` but not inside `.card-footer`. This provides native CSS scoping without Shadow DOM or class-based naming conventions like BEM.',
                'options'     => [
                    ['text' => 'Limits CSS rules to a DOM subtree, with an optional lower boundary to exclude nested parts', 'correct' => true],
                    ['text' => 'Sets the scope of CSS custom properties to a specific component tree', 'correct' => false],
                    ['text' => 'An alias for the :scope pseudo-class used in selector matching', 'correct' => false],
                    ['text' => 'Scopes animation keyframes to a single element to prevent name conflicts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is native CSS nesting?',
                'explanation' => 'Native CSS nesting (without preprocessors) allows writing nested selectors directly in CSS. A child selector inside a parent rule: `.card { color: red; .title { font-size: 2rem; } &:hover { opacity: 0.9; } }`. The `&` references the parent selector. Nesting eliminates the need for preprocessors like Sass just for nesting and is now supported in all modern browsers.',
                'options'     => [
                    ['text' => 'Writing child selectors directly inside a parent rule using & for the parent reference — no preprocessor needed', 'correct' => true],
                    ['text' => 'Placing one CSS stylesheet inside another using @import nesting syntax', 'correct' => false],
                    ['text' => 'A CSS-in-JS technique for co-locating component styles with JavaScript', 'correct' => false],
                    ['text' => 'Using deeply nested HTML elements to increase selector specificity automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are CSS trigonometric functions and where are they used?',
                'explanation' => 'CSS now supports `sin()`, `cos()`, `tan()`, `asin()`, `acos()`, `atan()`, and `atan2()` as mathematical functions inside `calc()` or directly as values. They are primarily used for precise geometric layouts — for example, placing items in a circle: `left: calc(50% + cos(var(--angle)) * var(--radius))`. They allow pure CSS solutions to problems that previously required JavaScript math.',
                'options'     => [
                    ['text' => 'sin(), cos(), tan() etc. — math functions for geometric calculations like circular layouts in pure CSS', 'correct' => true],
                    ['text' => 'Animation timing functions that create trigonometric easing curves', 'correct' => false],
                    ['text' => 'Grid functions for creating triangular grid patterns', 'correct' => false],
                    ['text' => 'Transform shorthand for rotate and skew using degree values', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `color-mix()` CSS function do?',
                'explanation' => '`color-mix()` mixes two colours in a specified colour space. Example: `color-mix(in oklch, blue 40%, red)` mixes 40% blue and 60% red in the oklch colour space. The colour space matters — mixing in oklch produces perceptually uniform results, while mixing in sRGB can produce muddy intermediates. It enables dynamic colour theming previously requiring JavaScript or preprocessors.',
                'options'     => [
                    ['text' => 'Mixes two colours in a specified colour space, enabling dynamic colour calculations in CSS', 'correct' => true],
                    ['text' => 'Blends the foreground and background colours of an element using blend mode logic', 'correct' => false],
                    ['text' => 'A Sass function that has been standardised into native CSS', 'correct' => false],
                    ['text' => 'Creates a gradient midpoint between two colour stops in a linear-gradient', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `oklch` colour space in CSS and why is it preferred for design systems?',
                'explanation' => '`oklch` (Lightness, Chroma, Hue) is a perceptually uniform colour space available via `oklch(0.7 0.15 240)`. Unlike sRGB/HSL, equal numeric changes in oklch produce equal perceived brightness changes. This makes it ideal for generating colour palettes (e.g., shades of a brand colour at consistent perceived lightness). It also accesses a wider gamut of colours on P3 displays.',
                'options'     => [
                    ['text' => 'A perceptually uniform colour space where equal numeric steps produce equal perceived changes — ideal for palettes', 'correct' => true],
                    ['text' => 'An extension of oklch that adds an alpha channel for transparency control', 'correct' => false],
                    ['text' => 'A CSS colour profile for calibrated print output (CMYK-based)', 'correct' => false],
                    ['text' => 'The default colour space used by all CSS colour functions since CSS Color Level 3', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is CSS anchor positioning?',
                'explanation' => 'CSS anchor positioning (`anchor-name` and `position-anchor`) allows an element to be positioned relative to another element anywhere in the DOM — not just its parent. An anchor element is named with `anchor-name: --my-anchor` and the positioned element uses `position-anchor: --my-anchor` with `anchor()` function offsets: `top: anchor(bottom)`. This is the native CSS solution for tooltips, popovers, and dropdowns.',
                'options'     => [
                    ['text' => 'Positions an element relative to any named anchor element in the DOM using anchor-name and position-anchor', 'correct' => true],
                    ['text' => 'Creates anchor links that scroll to a specific position on the page', 'correct' => false],
                    ['text' => 'Fixes an element\'s position to an anchor point at the corner of the viewport', 'correct' => false],
                    ['text' => 'A CSS Grid feature for anchoring items to specific grid lines', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `overscroll-behavior: contain` do?',
                'explanation' => '`overscroll-behavior` controls what happens when scrolling reaches the edge of a scroll container. `contain` prevents scroll chaining — the parent does not scroll when the child reaches its boundary. `none` also prevents the browser\'s bounce/rubber-band effect at page edges. `auto` (default) allows normal scroll chaining. Useful for modal dialogs and sidebars that should not scroll the page behind them.',
                'options'     => [
                    ['text' => 'Prevents scroll chaining so the parent does not scroll when the child reaches its scroll boundary', 'correct' => true],
                    ['text' => 'Constrains scroll to only occur within the element\'s padding box', 'correct' => false],
                    ['text' => 'Prevents the element from overscrolling beyond its content size in any direction', 'correct' => false],
                    ['text' => 'Removes the browser\'s overscroll animation and bounce effect on all devices', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `scroll-snap-type: y mandatory` do in CSS?',
                'explanation' => '`scroll-snap-type` enables CSS scroll snapping on a scroll container. `y` means snapping occurs on the vertical axis. `mandatory` means the browser always snaps to the nearest snap point after scrolling — the user cannot stop between snaps. `proximity` only snaps if the scroll position is close to a snap point. Snap points on children are set with `scroll-snap-align: start | center | end`.',
                'options'     => [
                    ['text' => 'Enables mandatory vertical scroll snapping — the container always snaps to the nearest child snap point', 'correct' => true],
                    ['text' => 'Makes the scroll container mandatory for all child elements to scroll within', 'correct' => false],
                    ['text' => 'Locks the scroll position to the top of the container after any scroll event', 'correct' => false],
                    ['text' => 'Sets the scroll type to snap and the scroll-y axis to be mandatory for accessibility', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `@media (prefers-reduced-motion: reduce)` target?',
                'explanation' => '`prefers-reduced-motion` detects whether the user has requested reduced motion in their operating system accessibility settings. Users with vestibular disorders can be harmed by excessive animation. Targeting `reduce` allows you to disable or slow down animations/transitions for these users: remove `transform` animations, reduce `transition` durations, or replace `@keyframes` with simple opacity fades.',
                'options'     => [
                    ['text' => 'Targets users who have enabled reduced motion in their OS accessibility settings', 'correct' => true],
                    ['text' => 'Reduces the motion blur effect applied to fast-moving CSS animations', 'correct' => false],
                    ['text' => 'Targets low-end devices to reduce animation complexity for performance', 'correct' => false],
                    ['text' => 'A media query for battery saver mode that disables CSS animations globally', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `@media (forced-colors: active)` media query target?',
                'explanation' => '`forced-colors: active` detects Windows High Contrast Mode (or similar OS-level forced colour modes) where the operating system replaces all colours with a restricted palette for accessibility. CSS properties like `background-color`, `color`, and `border-color` are overridden by the OS. Use this media query to ensure custom styled elements (e.g., SVGs, pseudo-elements) remain visible by using `forced-color-adjust: auto` or the `ButtonText`/`CanvasText` system colour keywords.',
                'options'     => [
                    ['text' => 'Detects Windows High Contrast Mode where the OS forces a restricted colour palette for accessibility', 'correct' => true],
                    ['text' => 'Targets users who have set a forced dark or light mode in their browser settings', 'correct' => false],
                    ['text' => 'Detects when a printer forces black-and-white colour output', 'correct' => false],
                    ['text' => 'Activates when the user\'s display colour profile is forced to sRGB', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are best practices for `@media print` CSS?',
                'explanation' => '`@media print` applies styles only when the page is printed or saved as PDF. Best practices: hide navigation, sidebars, and ads (`display: none`); use black text on white background; expand links to show URLs (`a::after { content: " (" attr(href) ")"; }`); avoid `background-color` unless `print-color-adjust: exact` is set; use `page-break-before`/`after`/`inside` (or modern `break-before`/`after`) to control page breaks.',
                'options'     => [
                    ['text' => 'Hide navigation/ads, use black-on-white, show link URLs via ::after content, control page breaks', 'correct' => true],
                    ['text' => 'Print styles are automatic — browsers apply sensible defaults and no @media print rules are needed', 'correct' => false],
                    ['text' => 'Use @media print only to set paper size; all other styles are inherited from screen styles', 'correct' => false],
                    ['text' => '@media print only works with external stylesheets, not inline or internal style tags', 'correct' => false],
                ],
            ],
        ];
    }
}
