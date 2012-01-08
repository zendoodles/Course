<?php

/**
 * @file
 * This file contains no working PHP code; it exists to provide additional
 * documentation for doxygen as well as to document hooks in the standard
 * Drupal manner.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
* Act on courses being loaded from the database.
*
* This hook is invoked during course loading, which is handled by
* entity_load(), via the EntityCRUDController.
*
* @param $entities
*   An array of course entities being loaded, keyed by id.
*
* @see hook_entity_load()
*/
function hook_course_load($entities) {
  $result = db_query('SELECT course_id, foo FROM {mytable} WHERE course_id IN(:ids)', array(':ids' => array_keys($entities)));
  foreach ($result as $record) {
    $entities[$record->course_id]->foo = $record->foo;
  }
}

/**
* Respond when a course is inserted.
*
* This hook is invoked after the course is inserted into the database.
*
* @param course
*   The course that is being inserted.
*
* @see hook_entity_insert()
*/
function hook_course_insert($course) {
  db_insert('mytable')
    ->fields(array(
      'course_id' => $course->course_id,
      'extra' => $course->extra,
    ))
    ->execute();
}

/**
* Act on a course being inserted or updated.
*
* This hook is invoked before the course is saved to the database.
*
* @param $course
*   The course that is being inserted or updated.
*
* @see hook_entity_presave()
*/
function hook_course_presave($course) {
  $course->extra = 'foo';
}

/**
* Respond to a course being updated.
*
* This hook is invoked after the course has been updated in the database.
*
* @param $course
*   The $course that is being updated.
*
* @see hook_entity_update()
*/
function hook_course_update($course) {
  db_update('mytable')
    ->fields(array('extra' => $course->extra))
    ->condition('course_id', $course->course_id)
    ->execute();
}

/**
* Respond to course deletion.
*
* This hook is invoked after the course has been removed from the database.
*
* @param $course
*   The course that is being deleted.
*
* @see hook_entity_delete()
*/
function hook_course_delete($course) {
  db_delete('mytable')
    ->condition('course_id', $course->course_id)
    ->execute();
}

/**
* Act on a course that is being assembled before rendering.
*
* @param $course
*   The course entity.
* @param $view_mode
*   The view mode the course is rendered in.
* @param $langcode
*   The language code used for rendering.
*
* The module may add elements to $course->content prior to rendering. The
* structure of $course->content is a renderable array as expected by
* drupal_render().
*
* @see hook_entity_prepare_view()
* @see hook_entity_view()
*/
function hook_course_view($course, $view_mode, $langcode) {
  $course->content['my_additional_field'] = array(
    '#markup' => $additional_field,
    '#weight' => 10,
    '#theme' => 'mymodule_my_additional_field',
  );
}

/**
* Alter the results of entity_view() for courses.
*
* @param $build
*   A renderable array representing the course content.
*
* This hook is called after the content has been assembled in a structured
* array and may be used for doing processing which requires that the complete
* course content structure has been built.
*
* If the module wishes to act on the rendered HTML of the course rather than
* the structured content array, it may use this hook to add a #post_render
* callback. Alternatively, it could also implement hook_preprocess_course().
* See drupal_render() and theme() documentation respectively for details.
*
* @see hook_entity_view_alter()
*/
function hook_course_view_alter($build) {
  if ($build['#view_mode'] == 'full' && isset($build['an_additional_field'])) {
    // Change its weight.
    $build['an_additional_field']['#weight'] = -10;

    // Add a #post_render callback to act on the rendered HTML of the entity.
    $build['#post_render'][] = 'my_module_post_render';
  }
}

/**
 * Act on course type being loaded from the database.
 *
 * This hook is invoked during course type loading, which is handled by
 * entity_load(), via the EntityCRUDController.
 *
 * @param $types
 *   An array of courses being loaded, keyed by course type names.
 */
function hook_course_type_load($types) {
//@todo Finish this!
}

/**
 * Respond when a course type is inserted.
 *
 * This hook is invoked after the course type is inserted into the database.
 *
 * @param $type
 *   The course type that is being inserted.
 */
function hook_course_type_insert($type) {
  db_insert('mytable')
    ->fields(array(
      'id' => $type->id,
      'extra' => $type->extra,
    ))
    ->execute();
}

/**
 * Act on a course type being inserted or updated.
 *
 * This hook is invoked before the course type is saved to the database.
 *
 * @param $type
 *   The course type that is being inserted or updated.
 */
function hook_course_type_presave($type) {
  $type->extra = 'foo';
}

/**
 * Respond to updates to a course.
 *
 * This hook is invoked after the course type has been updated in the database.
 *
 * @param $type
 *   The course type that is being updated.
 */
function hook_course_type_update($type) {
  db_update('mytable')
    ->fields(array('extra' => $type->extra))
    ->condition('id', $type->id)
    ->execute();
}

/**
 * Respond to course type deletion.
 *
 * This hook is invoked after the course type has been removed from the
 * database.
 *
 * @param $type
 *   The course type that is being deleted.
 */
function hook_course_type_delete($type) {
  db_delete('mytable')
    ->condition('id', $type->id)
    ->execute();
}

/**
 * Define default course type configurations.
 *
 * @return
 *   An array of default course types, keyed by course type names.
 */
function hook_default_course_type() {
  $types['base'] = new CourseType(array(
      'type' => 'base',
      'label' => t('Course'),
      'weight' => 0,
      'locked' => TRUE,
  ));
  return $types;
}

/**
* Alter default course type configurations.
*
* @param $defaults
*   An array of default course types, keyed by type names.
*
* @see hook_default_course_type()
*/
function hook_default_course_type_alter(&$defaults) {
  $defaults['base']->label = 'custom label';
}

/**
 * Alter course forms.
 *
 * Modules may alter the course entity form regardless to which form it is
 * attached by making use of this hook or the course type specifiy
 * hook_form_course_edit_PROFILE_TYPE_form_alter(). #entity_builders may be
 * used in order to copy the values of added form elements to the entity, just
 * as described by entity_form_submit_build_entity().
 *
 * @param $form
 *   Nested array of form elements that comprise the form.
 * @param $form_state
 *   A keyed array containing the current state of the form.
 *
 * @see course_attach_form()
 */
function hook_form_course_form_alter(&$form, &$form_state) {
  // Your alterations.
}

/**
 * Control access to courses.
 *
 * Modules may implement this hook if they want to have a say in whether or not
 * a given user has access to perform a given operation on a course.
 *
 * @param $op
 *   The operation being performed. One of 'view', 'edit' (being the same as
 *   'create' or 'update') and 'delete'.
 * @param $course
 *   (optional) A course to check access for. If nothing is given, access for
 *   all courses is determined.
 * @param $account
 *   (optional) The user to check for. If no account is passed, access is
 *   determined for the global user.
 * @return boolean
 *   Return TRUE to grant access, FALSE to explicitly deny access. Return NULL
 *   or nothing to not affect the operation.
 *   Access is granted as soon as a module grants access and no one denies
 *   access. Thus if no module explicitly grants access, access will be denied.
 *
 * @see course_access()
 */
function hook_course_access($op, $course = NULL, $account = NULL) {
  if (isset($course)) {
    // Explicitly deny access for a course type.
    if ($course->type == 'secret' && !user_access('custom permission')) {
      return FALSE;
    }
    // For courses other than the default course grant access.
    if ($course->type != 'base' && user_access('custom permission')) {
      return TRUE;
    }
    // In other cases do not alter access.
  }
}

/**
 * @}
 */
