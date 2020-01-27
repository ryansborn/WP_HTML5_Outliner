# Changelog

## 1.3.0 (2020-1-26)
- Change text domain to plugin slug.
- Add i18n support to JS files.
- Edit readme section titled “Why do these outlines matter?”.

## 1.2.0 (2019-4-14)
- Add function to load plugin's text domain.
- Ensure 'No outline was created' message replaces heading-level outline if source has no headings.
- Remove reference operator and expand comments in Source_Loader::load().
- Refactor Node_analyzer::analyze() and Node_analyzer::is_sectioning_root().
- Refactor Document_Outline_Algorithm::get_rank().
- Correct @package tag wherever used.
- Correct @var tag for `$stack` in Document_Outline_Algorithm.
- Remove phpdocs and phpmetrics.

## 1.1.0 (2018-12-8)
- Fix notices for empty headings in an hgroup that also contains non-heading elements.
- Shorten Toolbar node title to "Outline".
- Update screenshots to show use in WordPress 5.0. 
- Rename "HTML5_Outline" class to "Document_Outline".
- Refactor Document_Outline_Algorithm::get_rank() and Document_Outline_Algorithm::get_ranking_heading().
- Add description to DocBLock for Outline class.
- Condense description in DocBLock for Document_Outline_Algorithm class.
- Correct the DocBlock for Document_Outline::outline().
- Update phpDocumentor docs and PhpMetrics.
- Update README.
- Improve spacing and indentation in source.

## 1.0.0 (2018-11-27)
- Release the plugin.
