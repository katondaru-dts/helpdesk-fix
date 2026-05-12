<?php

namespace App\Helpers;

class MarkdownHelper
{
    public static function toHtml(string $md): string
    {
        $md = str_replace("\r\n", "\n", $md);
        $lines = explode("\n", $md);
        $html = '';
        $inCode = false;
        $codeLang = '';
        $codeBuffer = [];
        $inList = false;
        $listType = '';

        foreach ($lines as $line) {
            // Fenced code block
            if (preg_match('/^```(\w*)$/', $line, $m)) {
                if (!$inCode) {
                    if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                    $inCode = true;
                    $codeLang = $m[1];
                } else {
                    $inCode = false;
                    $lang = $codeLang ? " class=\"language-{$codeLang}\"" : '';
                    $html .= "<pre><code{$lang}>" . htmlspecialchars(implode("\n", $codeBuffer)) . "</code></pre>\n";
                    $codeBuffer = [];
                    $codeLang = '';
                }
                continue;
            }
            if ($inCode) { $codeBuffer[] = $line; continue; }

            // Headings
            if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $m)) {
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                $level = strlen($m[1]);
                $html .= "<h{$level}>" . self::inline($m[2]) . "</h{$level}>\n";
                continue;
            }

            // Horizontal rule
            if (preg_match('/^[-*_]{3,}$/', trim($line))) {
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                $html .= "<hr>\n";
                continue;
            }

            // Unordered list
            if (preg_match('/^[-*+]\s+(.+)$/', $line, $m)) {
                if (!$inList || $listType !== 'ul') {
                    if ($inList) $html .= "</{$listType}>\n";
                    $html .= "<ul>\n"; $inList = true; $listType = 'ul';
                }
                $html .= "<li>" . self::inline($m[1]) . "</li>\n";
                continue;
            }

            // Ordered list
            if (preg_match('/^\d+\.\s+(.+)$/', $line, $m)) {
                if (!$inList || $listType !== 'ol') {
                    if ($inList) $html .= "</{$listType}>\n";
                    $html .= "<ol>\n"; $inList = true; $listType = 'ol';
                }
                $html .= "<li>" . self::inline($m[1]) . "</li>\n";
                continue;
            }

            // Blockquote
            if (preg_match('/^>\s?(.*)$/', $line, $m)) {
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                $html .= "<blockquote>" . self::inline($m[1]) . "</blockquote>\n";
                continue;
            }

            // Empty line
            if (trim($line) === '') {
                if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
                $html .= "\n";
                continue;
            }

            // Paragraph
            if ($inList) { $html .= "</{$listType}>\n"; $inList = false; }
            $html .= "<p>" . self::inline($line) . "</p>\n";
        }

        if ($inList) $html .= "</{$listType}>\n";
        if ($inCode && $codeBuffer) {
            $html .= "<pre><code>" . htmlspecialchars(implode("\n", $codeBuffer)) . "</code></pre>\n";
        }

        return $html;
    }

    private static function inline(string $text): string
    {
        // Escape HTML first (except we'll re-add allowed tags)
        // Bold + italic
        $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $text);
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/_(.+?)_/', '<em>$1</em>', $text);
        // Strikethrough
        $text = preg_replace('/~~(.+?)~~/', '<del>$1</del>', $text);
        // Inline code
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);
        // Image before link (order matters)
        $text = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1" style="max-width:100%">', $text);
        // Link
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }
}
