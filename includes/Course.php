<?php/** * @file * Course Class definition. */Class Course extends Entity {  public $course_id;    // The course id  public $type;         // Machine name of the course type  public $label;        // Human readable label for the course type  public $created;      // Creation date of course (UNIX timestamp)  public $changed;      // Last changed date (UNIX timestamp)  public function __construct($values = array()) {    parent::__construct($values, 'course');  }  /**   * Returns the full url() for the course.   * @return string   */  public function url() {    $uri = $this->url();    return url($uri['path'], $uri);  }  /**   * Returns the drupal path to this course.   */  public function path() {    $uri = $this->uri();    return $uri['path'];  }  public function buildContent($view_mode = 'full', $langcode = NULL) {    $content = array();    // Assume newly create objects are still empty.    if (!empty($this->is_new)) {      $content['empty']['#markup'] = '<em class="course-no-data">' . t('There is no course data yet.') . '</em>';    }    return entity_get_controller($this->entityType)->buildContent($this, $view_mode, $langcode, $content);  }  public function save() {    if (empty($this->created) && (!empty($this->is_new) || !$this->course_id)) {      $this->created = REQUEST_TIME;    }    $this->changed = REQUEST_TIME;    parent::save();    if (isset($cache[$this->course_id])) {      $cache[$this->course_id][$this->type] = $this->course_id;    }  }}/** * Use a separate class for course types so we can specify some defaults * modules may alter. */class CourseType extends Entity {  public $type;  public $weight = 0;  public function __construct($values = array()) {    parent::__construct($values, 'course_type');  }  /**   * Returns whether the course type is locked, thus may not be deleted or renamed.   *   * Course types provided in code are automatically treated as locked, as well   * as any fixed course type.   */  public function isLocked() {    return isset($this->status) && empty($this->is_new) && (($this->status & ENTITY_IN_CODE) || ($this->status & ENTITY_FIXED));  }}