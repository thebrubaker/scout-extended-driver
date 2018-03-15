<?php

namespace App\Search\Algolia\Concerns;

trait HasHighlightSnippetSettings
{
    /**
     * List of attributes to highlight.
     *
     * If set to null, all searchable attributes are highlighted
     * (see searchableAttributes). The special value * may be
     * used to highlight all attributes.
     *
     * Only string values can be highlighted. Numerics will be ignored.
     *
     * List of attributes to highlight.
     *
     * If set to null, all searchable attributes are highlighted
     * (see searchableAttributes). The special value * may be used to
     * highlight all attributes.
     *
     * Only string values can be highlighted. Numerics will be ignored.
     *
     * When highlighting is enabled, each hit in the response will contain
     * an additional _highlightResult object (provided that at least one of
     * its attributes is highlighted).
     *
     * @var array
     */
    protected $attributesToHighlight = ['*'];

    /**
     * List of attributes to snippet, with an optional maximum number of words to snippet.
     *
     * If set to null, no attributes are snippeted. The special value * may
     * be used to snippet all attributes.
     *
     * The syntax for each attribute is ${attributeName}:${nbWords}. The
     * number of words can be omitted, and defaults to 10.
     *
     * Only string values can be snippeted. Numerics will be ignored.
     *
     * When snippeting is enabled, each hit in the response will contain an
     * additional _snippetResult object (provided that at least one of its attributes
     * is snippeted).
     *
     * @var array
     */
    protected $attributesToSnippet = [];

    /**
     * String inserted before highlighted parts in highlight and snippet results.
     *
     * @var string
     */
    protected $highlightPreTag = '<em>';

    /**
     * String inserted after highlighted parts in highlight and snippet results.
     *
     * @var string
     */
    protected $highlightPostTag = '</em>';

    /**
     * String used as an ellipsis indicator when a snippet is truncated.
     *
     * @var string
     */
    protected $snippetEllipsisText = '...';

    /**
     * Restrict arrays in highlight and snippet results to items that matched
     * the query.
     *
     * When false, all array items are highlighted/snippeted. When true, only
     * array items that matched at least partially are highlighted/snippeted.
     *
     * @var boolean
     */
    protected $restrictHighlightAndSnippetArrays = false;

    /**
     * Return the highlight and snippet settings.
     * @return array
     */
    public function getHighlightSnippetSettings()
    {
        return [
            'attributesToHighlight'             => $this->attributesToHighlight,
            'attributesToSnippet'               => $this->attributesToSnippet,
            'highlightPreTag'                   => $this->highlightPreTag,
            'highlightPostTag'                  => $this->highlightPostTag,
            'snippetEllipsisText'               => $this->snippetEllipsisText,
            'restrictHighlightAndSnippetArrays' => $this->restrictHighlightAndSnippetArrays,
        ];
    }
}
