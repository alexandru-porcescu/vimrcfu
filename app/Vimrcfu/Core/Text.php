<?php namespace Vimrcfu\Core;

use Michelf\MarkdownExtra;

class Text {

  /**
   * Remove unwanted HTML tags.
   *
   * @param string $text
   * @return string
   */
  private function stripTags($text)
  {
    $regex = "/<(?!\/?(em|strong|code|blockquote|p|br|kbd)(?=>))\/?.*?>/";

    return preg_replace($regex, '', $text);
  }

  /**
   * Creates clickable links from URLs.
   *
   * @param string $text
   * @return string
   */
  private function createExternalLinks($text)
  {
    $regex = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,63}([\w\.\/:\=\?\#\!-]*)/";

    return preg_replace($regex, '<a href="$0" target="_blank">$0</a>', $text);
  }

  /**
   * Constructs clickable internal links to Snippets.
   *
   * @param string $text
   * @param bool $absolute
   * @return string
   */
  private function createSnippetLinks($text, $absolute = false)
  {
    $regex = "/snippet#([0-9]*)(\/\S*)?/";
    if ( $absolute )
    {
      return preg_replace($regex, '<a href="' . \Config::get('app.url') . '/snippet/$1">Snippet #$1</a>', $text);
    }

    return preg_replace($regex, '<a href="/snippet/$1">Snippet #$1</a>', $text);
  }

  /**
   * Returns HTML from Markdown
   *
   * @param string $text
   * @return string
   */
  private function renderMarkdown($text)
  {
    return MarkdownExtra::defaultTransform($text);
  }

  /**
   * Returns HTML without unwanted tags
   * with external and internal links.
   *
   * @param string $text
   * @return string
   */
  public function render($text)
  {
    $text = $this->renderMarkdown($text);
    $text = $this->stripTags($text);
    $text = $this->createExternalLinks($text);
    $text = $this->createSnippetLinks($text);

    return $text;
  }

  /**
   * Returns HTML from Markdown file with all tags intact.
   *
   * @param string $filename
   * @return string
   */
  public function renderInclude($filename)
  {
    $text = \File::get(app_path().'/markdown/'.$filename.'.md');
    $text = $this->renderMarkdown($text);
    $text = $this->createSnippetLinks($text);

    return $text;
  }

  /**
   * Returns rendered HTML with absolute links for RSS.
   *
   * @param string $text
   * @return string
   */
  public function renderForRss($text)
  {
    $text = $this->renderMarkdown($text);
    $text = $this->stripTags($text);
    $text = $this->createExternalLinks($text);
    $text = $this->createSnippetLinks($text, true);

    return $text;
  }

  /**
   * Escape special characters for XML output.
   *
   * @param string $text
   * @return string
   */
  public function xmlentities($text)
  {
    $text = str_replace('&', '&#x26;', $text);
    $text = str_replace('<', '&#x3C;', $text);
    $text = str_replace('>', '&#x3E;', $text);

    return $text;
  }

}
