Feature: Administration - Tableaux filtrables (JavaScript)

  @reloadDbWithTestData
  @javascript
  Scenario: TableFilter est initialisé avec ses feuilles de style servies par AssetMapper
    Given I am logged in as admin and on the Administration
    When I am on "/admin/accounting/journal/list?periodId=15"
    Then I should see "PRLV SEPA ONLINE SAS SCW SCALEWAY"
    And I should see a "table.afup-tab-filterable tr.fltrow" element
    And I should see "Lignes :"
    And I should see a "link[href^='/dist/tablefilter/style/tablefilter-']" element
    And I should see a "link[href^='/dist/tablefilter/style/themes/transparent/transparent-']" element
