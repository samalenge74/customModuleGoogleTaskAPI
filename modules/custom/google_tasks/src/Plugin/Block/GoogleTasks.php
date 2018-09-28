<?php

namespace Drupal\google_tasks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\social_auth_google;
use Google_Client;
use Google_Service_Tasks;

/**
 * Provides a 'Google Tasks' Block.
 *
 * @Block(
 *   id = "google_tasks",
 *   admin_label = @Translation("Google Tasks"),
 *   category = @Translation("Google Tasks"),
 * )
 */
class GoogleTasks extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    //Call the add task form
    $form = \Drupal::formBuilder()->getForm('Drupal\google_tasks\Form\GoogleTasksForm');

    //Authenticate before comsuming the Google Service Tasks API
    $client = social_auth_google('jsdeveloper74@gmail.com'); // this is not the right way to authenticate in the backend

    //create a new instance of Google Service Tasks
    $service = new Google_Service_Tasks($client);

    // $listofTasks will list all the Tasks Lists and tasks under them
    $listofTasks = "";
    $listofTasks .= "<h1>List of Tasks</h1>";
    // Print the first 10 task lists.
    $optParams = array(
      'maxResults' => 10,
    );
    //Get the list of tasks lists
    $results = $service->tasklists->listTasklists($optParams);
    $taskslists = array();

    //Count the number of items returned and if count equals, display no tasl lists found
    if (count($results->getItems()) == 0) {
      $listsOfTasks .= "No task lists found.";
      $listsOfTasks .= "<br >";
    } else {
      //Add to associative array the list id and title
      foreach ($results->getItems() as $tasklist) {
      	$taskslists = $this->array_push_assoc($taskslists, $tasklist->getId(), $tasklist->getTitle());
      }
      // loop through the associative array and display the list name and all tasks under it
      foreach($taskslists as $key => $value)
      {
      	$tasks = $service->tasks->listTasks($key);
      	$listsOfTasks .= "List of tasks under :" . $value . "<br ><br >";
        $listsOfTasks .= "<ul>";
      	foreach($tasks->getItems() as $task) {
      	  $listsOfTasks .= "<li>". $task->getTitle() . "</li>";
      	}
        $listsOfTasks .= "</ul>";
        $listsOfTasks .= "<br ><br >";
      }

    }
    //title added before add task form
    $listofTasks .= "<h1>Add a task using the form below</h1>";

    //display the list of tasks and the add task form
    return array(
      '#markup' => $this->t($listofTasks),
      $form,

    );
  }

  function array_push_assoc($array, $key, $value)
  {
    $array[$key] = $value;
    return $array;
  }

}
