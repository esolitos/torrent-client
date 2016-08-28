<?php
/**
 * Created by PhpStorm.
 * User: esolitos
 * Date: 26/08/16
 * Time: 21:03
 */

namespace Drupal\transmission_client\Form;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\transmission_client\TransmissionRpcClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigurationForm extends ConfigFormBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\transmission_client\TransmissionRpcClientInterface
   */
  protected $rpcClient;

  /**
   * @inheritDoc
   */
  public function __construct(ConfigFactoryInterface $config_factory, TransmissionRpcClientInterface $rpc_client) {
    parent::__construct($config_factory);

    $this->rpcClient = $rpc_client;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('transmission_client.rpc_client')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'transmission-client-configuration-form';
  }

  /**
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return [
      'transmission_client.config'
    ];
  }


  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('transmission_client.config');

    $form['rpc'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('RPC Config'),
      '#description' => $this->t('Please define the RPC configuration.'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      '#access' => $this->currentUser()->hasPermission('administer transmission '),
    );

    $form['rpc']['ip'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Server IP'),
      '#description' => $this->t('Please specify the server IP or hostname to use in the RPC connection.'),
      '#required' => TRUE,
      '#default_value' => $config->get('rpc.ip'),
    );

    $form['rpc']['port'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Server Port'),
      '#description' => $this->t('Please specify the port to use in the RPC connection.'),
      '#size' => 10,
      '#maxlength' => 10,
      '#required' => TRUE,
      '#default_value' => $config->get('rpc.port'),
    );

    $form['rpc']['path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('RPC Path'),
      '#description' => $this->t('Please specify the path where the RPC will respond.'),
      '#size' => 60,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('rpc.path'),
    );

    $form['rpc']['auth'] = array(
      '#type' => 'details',
      '#title' => $this->t('RPC Auth'),
      '#description' => $this->t('If the RPC server requires authentication please fill this items, otherwise leave it empty to disable authentication.'),
      '#open' => !empty($config->get('rpc.auth')),
      '#tree' => TRUE,
    );

    $form['rpc']['auth']['user'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#size' => 40,
      '#maxlength' => 255,
      '#default_value' =>  $config->get('rpc.auth.user'),
    );

    $form['rpc']['auth']['pass'] = array(
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#size' => 40,
      '#maxlength' => 255,
    );

    if ( empty($form_state->getUserInput()) && $config->get('rpc.ip') ) {
      if ($version = $this->testConnection($config)) {
        drupal_set_message(t("The connection to Transmission on %host was successful.", ['%host'=>$config->get('rpc.ip')]));
        $form['rpc']['version'] = array(
          '#markup' => $version,
          '#prefix' => '<p class="transmission-versions"><em>',
          '#suffix' => '</em></p>',
        );
      }
      else {
        drupal_set_message(t("Unable to connect to %host.", ['%host'=>$config->get('rpc.ip')]), 'error');
      }
    }


    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('transmission_client.config');

    $config->set('rpc.ip', $form_state->getValue(['rpc','ip']));
    $config->set('rpc.port', $form_state->getValue(['rpc','port']));
    $config->set('rpc.path', $form_state->getValue(['rpc','path']));

    if ( $auth_user = $form_state->getValue(['rpc','auth','user'], FALSE) ) {
      $config->set('rpc.auth.user', $auth_user);
      $auth_pass = $form_state->getValue(['rpc','auth','pass']);

      // Only set a password if specified, otherwise keep the old one.
      if ( !empty($auth_pass) ) {
        $config->set('rpc.auth.pass', $auth_pass);
      }
    }
    else {
      // If username is empty reset the
      $config->set('rpc.auth', FALSE);
    }

    $config->save();
  }

  /**
   * @param $config
   * @return false|string
   */
  private function testConnection($config) {
    return $this->rpcClient->testConnection(
      $config->get('rpc.ip'),
      $config->get('rpc.port'),
      $config->get('rpc.path'),
      $config->get('rpc.auth.user'),
      $config->get('rpc.auth.pass')
    );
  }


}