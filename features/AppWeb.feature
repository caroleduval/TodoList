Feature: AppWeb

  Scenario: Homepage
    Given I am on the homepage
    Then I should see "Créer un utilisateur"

  @javascript
  Scenario: Connexion
    Given I am on "/login"
    When I fill in "_username" with "username"
    And I fill in "Mot de passe :" with "password"
    And I press "Se connecter"
    Then I should be on the homepage
