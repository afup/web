Feature: Administration - Socle JavaScript (jQuery, Semantic UI)

  @reloadDbWithTestData
  @javascript
  Scenario: jQuery 3 est chargé avant Semantic UI, dont les plugins sont disponibles
    Given I am logged in as admin and on the Administration
    When I am on "/admin/accounting/journal/list?periodId=15"
    Then I should see "PRLV SEPA ONLINE SAS SCW SCALEWAY"
    And the JavaScript expression "typeof window.jQuery === 'function' && /^3\./.test(window.jQuery.fn.jquery)" should be true
    And the JavaScript expression "typeof window.jQuery.fn.dropdown === 'function' && typeof window.jQuery.fn.modal === 'function'" should be true
