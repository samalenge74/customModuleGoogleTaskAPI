<?php
/**
 * @file
 * Contains \Drupal\google_tasks\Form\GoogleTasksForm.
 */
namespace Drupal\google_tasks\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_auth_google;
use Google_Client;
use Google_Service_Tasks;

class GoogleTasksForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'google_tasks_form';
  }

  /**
   * {@inheritdoc}
   */
  // build a form that contains two input fields and a submit button
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['task_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Task Title:'),
      '#required' => TRUE,
    );
    $form['task_due_date'] = array (
      '#type' => 'date',
      '#title' => t('Due Date'),
      '#required' => TRUE,
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Add Tasks'),
      '#button_type' => 'primary',
    );
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Add task and return message to Drupal front end.
    $result = $this->addTask($form_state->getValue('task_title'), $form_state->getValue('task_due_date'));
    drupal_set_message($this->t('Task @task_title has been successfully added!', array('@task_title' => $result)));
  }

  //this function is used to add a new task through Google Tasks API
  function addTask($tasksTitle, $dueDate) {
    $client = social_auth_google('jsdeveloper74@gmail.com'); // authentication issues
    $service = new Google_Service_Tasks($client);
    $task->setTitle($tasksTitle);
    $task->setNotes('Please complete me');
    $task->setDue(new TaskDateTime($dueDate));

    $result = $service->tasks->insertTasks('@default', $task);
    return $result->getId();
  }
}
