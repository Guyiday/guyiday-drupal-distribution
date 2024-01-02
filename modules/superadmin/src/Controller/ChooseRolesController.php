<?php

namespace Drupal\superadmin\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

class ChooseRolesController extends FormBase {
  public static function accessAllowed(AccountInterface $account) {
    $allowed = $account && (int) $account->id() === 1;
    return AccessResult::allowedIf($allowed);
  }

  public function getFormId() {
    return 'superadmin_choose_roles';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var Role[] $roles */
    $roles = Role::loadMultiple();

    $show_checkboxes = count($roles) > 3;

    $table = [
      '#type' => 'table',
      '#header' => [
        'checkbox' => '',
        'role' => 'Role',
        'button' => '',
      ]
    ];

    if (!$show_checkboxes) {
      unset($table['#header']['checkbox']);
    }

    foreach ($roles as $role) {
      $table[$role->id()] = [
        'checkbox' => [
          '#type' => 'checkbox',
          '#return_value' => 1,
        ],
        'label' => [
          '#markup' => $role->label()
        ],
        'button' => $this->getButton($role),
      ];

      if ($role->id() === Role::ANONYMOUS_ID || $role->id() === Role::AUTHENTICATED_ID) {
        $table[$role->id()]['checkbox']['#attributes']['disabled'] = TRUE;
      }
      if (!$show_checkboxes) {
        unset($table[$role->id()]['checkbox']);
      }
    }

    $table['superadmin'] = [
      'checkbox' => [
        '#type' => 'checkbox',
        '#attributes' => [
          'disabled' => TRUE,
        ]
      ],
      'label' => [
        '#markup' => 'Superadmin'
      ],
      'button' => $this->getSuperadminButton(),
    ];

    if (!$show_checkboxes) {
      unset($table['superadmin']['checkbox']);
    }

    // Move 'log out' entry to the end of the table.
    $log_out = $table[Role::ANONYMOUS_ID];
    unset($table[Role::ANONYMOUS_ID]);
    $table[Role::ANONYMOUS_ID] = $log_out;

    return [
      '#markup' => "<p>It's best to use Drupal with one or more roles that the client will also use. This way, you automatically test if the CMS is usable for others.</p>" .
        "<p>You need to select one or more roles before you can continue.</p>",
      'roles' => $table,
      'multiple' => [
        '#type' => 'submit',
        '#value' => 'Log in with multiple roles',
        '#access' => $show_checkboxes
      ],
    ];
  }

  protected function getButton(Role $role) {
    if ($role->id() === Role::ANONYMOUS_ID) {
      return [
        '#type' => 'link',
        '#title' => 'Log out',
        '#url' => Url::fromRoute('user.logout'),
        '#attributes' => ['class' => ['button']],
      ];
    }

    return [
      '#type' => 'submit',
      '#value' => 'Use as ' . $role->label(),
      '#role_id' => $role->id(),
      '#attributes' => ['class' => ['button--primary']],
    ];
  }

  protected function getSuperadminButton() {
    return [
      '#type' => 'submit',
      '#is_superadmin' => TRUE,
      '#value' => 'Use without any permission checks',
      '#attributes' => ['class' => ['button--danger']],
    ];
  }

  public function submitForm(array &$form, FormStateInterface $form_state){
    /** @var string[] $roles */
    $roles = [];
    $el = $form_state->getTriggeringElement();

    if (!empty($el['#is_superadmin'])) {
      $this->getRequest()->getSession()->set('chosen_superadmin', TRUE);
      $form_state->setRedirect('<front>');
      return;
    }

    if (!empty($el['#role_id'])) {
      if ($el['#role_id'] !== Role::AUTHENTICATED_ID) {
        $roles[] = $el['#role_id'];
      }
    }
    else {
      foreach ($form_state->getValues()['roles'] as $role_id => $values) {
        if ($values['checkbox']) {
          $roles[] = $role_id;
        }
      }
    }

    sort($roles);

    $username = \Drupal::currentUser()->getAccountName();
    if (empty($roles)) {
      $username .= "_authenticated";
    }
    foreach ($roles as $role) {
      $username .= "_$role";
    }

    /** @var User $user */
    $user = user_load_by_name($username);
    if (!$user) {
      $user = User::create();
      $user->setUsername($username);
    }
    $user->set('roles', $roles);
    $user->activate();
    $user->save();

    user_login_finalize($user);

    $form_state->setRedirect('<front>');
  }
}
