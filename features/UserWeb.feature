Feature: UserWebAsAdmin

  Scenario: Homepage not logged
    Given I go to the homepage
    Then I should be on "/login"

  Scenario: Connexion
    Given I am on "/login"
    When I fill in "_username" with "admin"
    And I fill in "Mot de passe :" with "password"
    And I press "Se connecter"
    Then I should be on the homepage

  Scenario: Aller sur la liste des utilisateurs
    Given I am logged in as "admin"
    And I am on the homepage
    Then print current URL
#    And I wait for 2 seconds
#    And I press "Utilisateurs"
#    Then I should be on "/users"

#  @javascript
#  Scenario: Aller sur la page de création d'un utilisateur
#    Given I am on "/users"
#    And I follow "Créer un utilisateur"
#    Then I should be on "/users/create"
#
#  @javascript
#  Scenario: Créer un utilisateur
#    Given I am on "/users/create"
#    When I fill in "Nom d'utilisateur" with "my_username"
#    And I fill in "Mot de passe" with "password"
#    And I fill in "Tapez le mot de passe à nouveau" with "password"
#    And I fill in "Adresse email" with "my_username@email.fr"
#    And I scroll
#    And I press "Ajouter"
#    Then I should be on "/users"
#    And I should see "my_username"
#
#  @javascript
#  Scenario: Créer un administrateur
#    Given I am on "/users/create"
#    When I fill in "Nom d'utilisateur" with "my_adminname"
#    And I fill in "Mot de passe" with "password"
#    And I fill in "Tapez le mot de passe à nouveau" with "password"
#    And I fill in "Adresse email" with "my_admin@email.fr"
#    And I scroll
#    And I check "Administrateur"
#    And I scroll
#    And I press "Ajouter"
#    Then I should be on "/users"
#    And I should see "my_admin"

