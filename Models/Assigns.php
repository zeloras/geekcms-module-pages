<?php

namespace GeekCms\Pages\Models;

use App\Models\MainModel;
use GeekCms\Pages\Models\db\Assigns as DBModel;

class Assigns extends DBModel
{
    public $post_data = [];
    private $pages_model;

    /**
     * {@inheritdoc}
     *
     * @param MainModel $model
     *
     * @return bool
     */
    public function saveAndValidate(MainModel $model)
    {
        $this->pages_model = $model;

        return parent::saveAndValidate($model);
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $update
     *
     * @return bool
     */
    public function dataSave($update = false)
    {
        $this->page_id = $this->pages_model->id;
        $this->enabled = (empty($this->enabled)) ? false : $this->enabled;

        /**
         * Get id's keys.
         */
        $current = $this;

        $edit_data = array_get($current->model_data_edit, 'block_id', []);
        $new_data = array_get($current->model_data_edit, 'block_id', []);
        $lists = ['edit' => $edit_data, 'new' => $new_data];

        foreach ($lists as $type_list => $data_list) {
            foreach ($data_list as $key_edit => $data_val) {
                if (0 === (int) $data_val) {
                    if ('edit' === $type_list) {
                        foreach ($current->model_data_edit as $mde_key => $mde_val) {
                            unset($current->model_data_edit[$mde_key][$key_edit]);
                        }
                    } else {
                        foreach ($current->model_data_new as $mde_key => $mde_val) {
                            unset($current->model_data_new[$mde_key][$key_edit]);
                        }
                    }
                }
            }
        }

        return $this->prepareMultiple($update, function ($key) use ($update, $current) {
            // Update or create project variables
            if ($update) {
                /**
                 * If variable not found, then create.
                 */
                $update_variable = self::where([
                    ['block_id', '=', $key],
                    ['page_id', '=', $current->page_id],
                ])->first();

                if ($update_variable) {
                    $update_variable->enabled = (bool) $current->enabled;
                    $update_variable->position = (int) $current->position;
                    $update_variable->save();
                } else {
                    $model_save = new $current($current->attributes);
                    $model_save->save();
                }
            } else {
                $model_save = new $current($current->attributes);
                $model_save->save();
            }
        }, function ($key, $value, $lists) use ($current, $update) {
            $assigns = data_get($current->pages_model, 'assigns', null);
            $removes = [];

            if (mb_strlen($value) < 1 || \count($assigns) > \count($lists['block_id'])) {
                if (\count($assigns) > \count($lists['block_id'])) {
                    foreach ($assigns as $assign) {
                        if (!\in_array($assign->block_id, $lists['block_id'], true)) {
                            $removes[$assign->id] = $assign->id;
                        }
                    }
                }

                if ($update) {
                    self::whereIn('id', $removes)->delete();
                }

                if (mb_strlen($value) < 1) {
                    return ['skip' => true];
                }
            }

            return [];
        });
    }
}
