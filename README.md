# WM Taxonomy Tree Shortcode

This is a Wordpress plugin that lets you render a taxonomy tree using a shortcode.

## Example of usage

You can use it in pages, posts or any place that accepts a shortcode.

```
[wm_taxonomy-tree
  taxonomy=category
  hide_empty=0
  number=1
  root_term_element=h1
  children_number=1 
  see_more_text="See all"
  container_class="taxonomy-tree-wrapper"
  term_class="my-generic-term"
  root_term_class="my-root-term"
]
```

> NOTE: Do not breakline inside wordpress posts, it can mess with the passed parameters. Use it all in one line. The above has line breaks for the sake of clarity.

## Parameters

All parameters from [wp_term_query](https://developer.wordpress.org/reference/classes/wp_term_query/__construct/) are accepted by this shortcode and will be passed to Wordpress function `get_terms`. E.g.: `number`, `parent`, `hide_empty`, etc. 

In addition to those above, this shortcode receives other parameters:
- `term_element`: The html element that will wrap every term title. Default: `span`.
- `root_term_element`: The html element that will wrap every root term title. If not passed, will fallback to `term_element`.
- `container_class`: The CSS class that will wrap the entire taxonomy tree. 
- `term_class`: The CSS class that will wrap every term title. Default: empty string.
- `root_term_class`: The CSS class that will wrap every root term title. If not passed, will fallback to `term_class`.
- `children_number`: Number of terms to show on nested terms. If not passed, will fallback to `number` used by `get_terms`. If not passed as well, will show all.
- `see_more_text`: The text to link to the parent term, in case that not all the child terms were presented. If not passed, the link will not be rendered.

## Default parameters
```php
[
  'term_element' => 'span',
  'parent' => 0,
  'number' => 0,
  'container_class' => '',
  'term_class' => '',
];
```
