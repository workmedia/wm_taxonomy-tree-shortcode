<?php
/**
 * Plugin Name: WM Taxonomy Tree Shortcode
 * Plugin URI: https://github.com/workmedia/wm_taxonomy-tree-shortcode
 * Description: Render a taxonomy tree in place of a shortcode
 * Version: 0.1.0
 * Author: Workmedia
 * Author URI: https://workmedia.pt
 */

function renderTitle($content, $level, $config) {
  $title = ($level === 0)
    ? (isset($config['root_term_element']) ? $config['root_term_element'] : $config['term_element'])
    : $config['term_element'];

  $termClass = ($level === 0) 
    ? (isset($config['root_term_class']) ? $config['root_term_class'] : $config['term_class'])
    : $config['term_class'];

  return "<$title class=\"taxonomy-term-name $termClass\">$content</$title>";
}

function renderSeeMore($term, $config) {
  if (!isset($config['see_more_text'])) {
    return '';
  }

  if (count($term->children) === $term->totalCount) {
    return '';
  }

  $see_more_text = $config['see_more_text'];

  return "<a href=\"$term->link\" class=\"taxonomy-term-see-more\">$see_more_text</a>";
};

function renderTaxonomyTerms($taxonomyTree, $config) {
  return '<ul>' . array_reduce(
    $taxonomyTree,
    function($str, $term) use ($config) {
      $children = renderTaxonomyTerms($term->children, $config);
      $title = renderTitle("<a href=\"$term->link\">$term->name</a>", $term->level, $config);
      $seeMore = renderSeeMore($term, $config);

      return "$str
        <li
          class=\"taxonomy-term\" 
          id=\"taxonomy-term-$term->term_id\" 
          data-level=\"$term->level\"
        >
          $title
          $children
          $seeMore
        </li>
        ";
    },
    ''
  ) . '</ul>';
}

function renderContainer($html, $config) {
  $containerClass = $config['container_class'];

  return "
    <div
      class=\"taxonomy-tree-container $containerClass\">
      $html
    </div>
  ";
}

function createTaxonomyTree($terms, $config, $level = 0) {
  return array_map(
    function($term) use ($config, $level) {
      $childrenConfig = [
        'parent' => $term->term_id,
        'number' => isset($config['children_number']) ? $config['children_number'] : $config['number'],
      ];
      $query = array_merge($config, $childrenConfig);
      $children = get_terms($query);

      return (object) array_merge(
        (array) $term,
        [
          'children' => createTaxonomyTree($children, $config, $level + 1),
          'link' => get_term_link($term),
          'level' => $level,
          'totalCount' => count(get_term_children( $term->term_id, $config['taxonomy']))
        ]
      );
    },
    $terms
  );
}

function getDefaults() {
  return [
    'term_element' => 'span',
    'parent' => 0,
    'number' => 0,
    'container_class' => '',
    'term_class' => '',
  ];
}

function sanitizeAtts($atts) {
  $atts = $atts ? $atts : [];

  $sanitezed_atts = [];

  foreach ($atts as $k => $v) {
    $key = wp_strip_all_tags($k);
    
    if ($key) {
      $sanitezed_atts[$key] = wp_strip_all_tags($v);
    }
  }

  return $sanitezed_atts + getDefaults();
}

add_shortcode('wm_taxonomy-tree', function($atts) {
  try {
    $atts = sanitizeAtts($atts);
    $terms = get_terms($atts);
  
    if (is_object($terms) && $terms->errors) {
      return '';
    }
  
    $taxonomyTree = createTaxonomyTree($terms, $atts);
    $taxonomyHtml = renderTaxonomyTerms($taxonomyTree, $atts);

    return renderContainer($taxonomyHtml, $atts);
  } catch (\Throwable $th) {
    if (WP_ENV === 'development') {
      throw $th;
    }

    return '';
  }
});
