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

  @javascript
  Scenario: Aller sur la page de création d'un utilisateurInscription en tant que user
    Given I am on the homepage
    And I press "Créer un utilisateur"
    Then I should be on "/users/create"

  @javascript
  Scenario: Inscription en tant que user
    Given I am on "/users/create"
    When I fill in "Nom d'utilisateur" with "my_username"
    And I fill in "Mot de passe :" with "password"
    And I fill in "Tapez le mot de passe à nouveau :" with "password"
    And I select "User" from "Rôles"
    And I press "Ajouter"
    Then I should be on the homepage
