Feature: UserWeb

  Scenario: Homepage not logged
    Given I go to the homepage
    Then I should be on "/login"

  Scenario: Connexion
    Given I am on "/login"
    When I fill in "_username" with "user"
    And I fill in "Mot de passe :" with "password"
    And I press "Se connecter"
    Then I should be on the homepage

  Scenario: Aller sur la liste des utilisateurs
    Given I am connected as "user" with password "password"
    And I go to "/users"
    Then the response status code should be 403

  Scenario: Aller sur la liste des taches
    Given I am connected as "user" with password "password"
    And I follow "Consulter la liste des tâches à faire"
    Then I should be on "/tasks/list"
    And I should see "Liste des tâches à faire"

  Scenario: Aller sur la page de création d'une tâche
    Given I am connected as "user" with password "password"
    And I go to "/tasks/list/0"
    And I follow "Créer une tâche"
    Then I should be on "/tasks/create"

  Scenario: Créer une tâche
    Given I am connected as "user" with password "password"
    And I go to "/tasks/create"
    When I fill in "task_title" with "titre de ma tâche"
    And I fill in "task_content" with "description de ma tâche"
    And I press "Ajouter"
    Then I should be on "/tasks/list"
    And I should see "titre de ma tâche"

  Scenario: Aller sur la page de modification d'une tâche
    Given I am connected as "user" with password "password"
    And I go to "/tasks/list/0"
    And I follow "Finaliser le projet"
    Then I should be on "/tasks/4/edit"

  Scenario: Modifier une tâche
    Given I am connected as "user" with password "password"
    And I go to "/tasks/4/edit"
    And I fill in "Description" with "description de ma tâche modifiée"
    And I press "Modifier"
    Then I should be on "/tasks/list"
    And I should see "description de ma tâche modifiée"

