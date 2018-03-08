Feature: UserWeb

  Scenario: Homepage
    Given I am on the homepage
    Then I should see "Créer un utilisateur"

  @javascript
  Scenario: Connexion
    Given I am on "/login"
    When I fill in "_username" with "username"
    And I fill in "Mot de passe :" with "password"
    And I scroll
    And I press "Se connecter"
    Then I should be on the homepage

  @javascript
  Scenario: Aller sur la page de création d'un utilisateur
    Given I am on the homepage
    And I follow "Créer un utilisateur"
    Then I should be on "/users/create"

  @javascript
  Scenario: Inscription en tant que user
    Given I am on "/users/create"
    When I fill in "Nom d'utilisateur" with "my_username"
    And I fill in "Mot de passe" with "password"
    And I fill in "Tapez le mot de passe à nouveau" with "password"
    And I fill in "Adresse email" with "myemail@mail.fr"
    And I scroll
    And I press "Ajouter"
    Then I should be on "/users"
    And I should see "my_username"

  @javascript
  Scenario: Inscription en tant que admin
    Given I am on "/users/create"
    When I fill in "Nom d'utilisateur" with "my_username2"
    And I fill in "Mot de passe" with "password"
    And I fill in "Tapez le mot de passe à nouveau" with "password"
    And I fill in "Adresse email" with "myemail2@mail.fr"
    And I scroll
    And I check "Administrateur"
    And I scroll
    And I press "Ajouter"
    Then I should be on "/users"
    And I should see "my_username2"


  @javascript
  Scenario: Modification d'un user -> admin
    Given I am on "/users"
    Then I follow "Edit" on the row containing "my_username"
    And I scroll
    And I check "Administrateur"
    And I press "Modifier"
    Then I should be on "/users"
