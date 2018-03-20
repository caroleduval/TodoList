Feature: AdminWeb

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
    Given I am connected as "admin" with password "password"
    And I go to "/users"
    Then I should see "Liste des utilisateurs"

  Scenario: Aller sur la page de création d'un utilisateur
    Given I am connected as "admin" with password "password"
    And I go to "/users"
    And I follow "Créer un utilisateur"
    Then I should be on "/users/create"

  Scenario: Créer un utilisateur
    Given I am connected as "admin" with password "password"
    And I go to "/users/create"
    When I fill in "Nom d'utilisateur" with "my_username"
    And I fill in "Mot de passe" with "password"
    And I fill in "Tapez le mot de passe à nouveau" with "password"
    And I fill in "Adresse email" with "my_username@email.fr"
    And I press "Ajouter"
    Then I should be on "/users"
    And I should see "my_username"

  Scenario: Aller sur la page de modification d'un utilisateur
    Given I am connected as "admin" with password "password"
    And I go to "/users"
    And I follow "Edit" on the row containing "username"
    Then I should see "Modifier username"

  Scenario: Modifier un utilisateur
    Given I am connected as "admin" with password "password"
    And I go to "/users/3/edit"
    And I fill in "Nom d'utilisateur" with "username_modif"
    And I check "Administrateur"
    And I press "Modifier"
    Then I should be on "/users"
    And I should see "username_modif"

  Scenario: Créer un administrateur
    Given I am connected as "admin" with password "password"
    And I go to "/users/create"
    When I fill in "Nom d'utilisateur" with "my_adminname"
    And I fill in "Mot de passe" with "password"
    And I fill in "Tapez le mot de passe à nouveau" with "password"
    And I fill in "Adresse email" with "my_admin@email.fr"
    And I check "Administrateur"
    And I press "Ajouter"
    Then I should be on "/users"
    And I should see "my_admin"

  Scenario: Aller sur la liste des taches
    Given I am connected as "admin" with password "password"
    And I follow "Consulter la liste des tâches à faire"
    Then I should be on "/tasks/list"
    And I should see "Liste des tâches à faire"

  Scenario: Aller sur la page de création d'une tâche
    Given I am connected as "admin" with password "password"
    And I go to "/tasks/list/0"
    And I follow "Créer une tâche"
    Then I should be on "/tasks/create"

  Scenario: Créer une tâche
    Given I am connected as "admin" with password "password"
    And I go to "/tasks/create"
    When I fill in "task_title" with "titre de ma tâche"
    And I fill in "task_content" with "description de ma tâche"
    And I press "Ajouter"
    Then I should be on "/tasks/list"
    And I should see "titre de ma tâche"

  Scenario: Aller sur la page de modification d'une tâche
    Given I am connected as "admin" with password "password"
    And I go to "/tasks/list/0"
    And I follow "Analyser le projet"
    Then I should be on "/tasks/2/edit"

  Scenario: Modifier une tâche
    Given I am connected as "admin" with password "password"
    And I go to "/tasks/2/edit"
    And I fill in "Description" with "description de ma tâche modifiée"
    And I press "Modifier"
    Then I should be on "/tasks/list"
    And I should see "description de ma tâche modifiée"
