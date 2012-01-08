<?php

/**
 * @file
 * Provides Entity metadata integration.
 */

/**
 * Extend the defaults.
 */
class courseMetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    $properties = &$info[$this->type]['properties'];

    $properties['label'] = array(
      'label' => t('Label'),
      'description' => t('The course label.'),
      'setter callback' => 'entity_property_verbatim_set',
      'setter permission' => 'administer courses',
      'schema field' => 'label',
    );

    $properties['type'] = array(
      'type' => 'course_type',
      'getter callback' => 'entity_property_getter_method',
      'setter callback' => 'entity_property_verbatim_set',
      'setter permission' => 'administer courses',
      'required' => TRUE,
      'description' => t('The course type.'),
    ) + $properties['type'];

    $properties['created'] = array(
      'label' => t("Date created"),
      'type' => 'date',
      'description' => t("The date the course was created."),
      'setter callback' => 'entity_property_verbatim_set',
      'setter permission' => 'administer courses',
      'schema field' => 'created',
    );
    $properties['changed'] = array(
      'label' => t("Date changed"),
      'type' => 'date',
      'schema field' => 'changed',
      'description' => t("The date the course was most recently updated."),
    );

    return $info;
  }
}

/**
 * Implements hook_entity_property_info_alter().
 */
function course_entity_property_info_alter(&$info) {
  // Add related courses.
  $properties = &$info['properties'];
  foreach (course_get_types() as $type_name => $type) {
    $properties['course_' . $type_name] = array(
      'type' => 'course',
      'label' => t('@type_name course', array('@type_name' => drupal_ucfirst($type->label))),
      'description' => t("The @type_name course.", array('@type_name' => $type->label)),
      'getter callback' => 'course_get_properties',
      'bundle' => $type_name,
    );
  }
}
