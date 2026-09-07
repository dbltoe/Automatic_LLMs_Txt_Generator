<?php
/**
 * LLMs.txt Generator -- admin menu labels and generated-file wording (English).
 *
 * The BOX_* entries are the `language_key` values the installer writes into
 * admin_pages, so they must exist before the menus are drawn -- hence
 * extra_definitions, which every supported release loads for the admin.
 *
 * The LLMSTXT_HEADING_* and LLMSTXT_LABEL_* entries are the words that appear
 * in the generated file itself. They live in extra_definitions on BOTH sides
 * (this file, and the storefront's) because the file can be built from either.
 * The generator falls back to the same English if a key is missing, so a
 * translation may override any subset.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

$define = [
    'BOX_CONFIGURATION_LLMS_TXT' => 'LLMs.txt Generator',
    'BOX_TOOLS_LLMS_TXT' => 'LLMs.txt Generator',

    'LLMSTXT_DEFAULT_SUMMARY' => '%s is an online store.',
    'LLMSTXT_HEADING_INFO' => 'Shopping information',
    'LLMSTXT_HEADING_CATEGORIES' => 'Categories',
    'LLMSTXT_HEADING_PRODUCTS' => 'Products',
    'LLMSTXT_HEADING_PAGES' => 'More pages',
    'LLMSTXT_LABEL_WEBSITE' => 'Website',
    'LLMSTXT_LABEL_CURRENCY' => 'Prices are in',
    'LLMSTXT_LABEL_SITEMAP' => 'Sitemap',
    'LLMSTXT_LABEL_FULL' => 'Full catalog listing',
    'LLMSTXT_NOTE_FULL' => 'the same sections with complete descriptions',
    'LLMSTXT_LABEL_MODEL' => 'Model',
    'LLMSTXT_LABEL_SHIPPINGINFO' => 'Shipping and Returns',
    'LLMSTXT_LABEL_PRIVACY' => 'Privacy Notice',
    'LLMSTXT_LABEL_CONDITIONS' => 'Conditions of Use',
    'LLMSTXT_LABEL_CONTACT_US' => 'Contact Us',
    'LLMSTXT_LABEL_SITE_MAP' => 'Site Map',
    'LLMSTXT_LABEL_GV_FAQ' => 'Gift Certificate FAQ',
    'LLMSTXT_LABEL_DISCOUNT_COUPON' => 'Discount Coupons',
    'LLMSTXT_LABEL_SPECIALS' => 'Specials',
    'LLMSTXT_LABEL_PRODUCTS_NEW' => 'New Products',
    'LLMSTXT_LABEL_PRODUCTS_ALL' => 'All Products',
    'LLMSTXT_LABEL_FEATURED_PRODUCTS' => 'Featured Products',
    'LLMSTXT_LABEL_REVIEWS' => 'Customer Reviews',
];

return $define;
