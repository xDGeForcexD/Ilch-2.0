<?php
/**
 * Holds Admin_ModuleMapper.
 *
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Admin\Mappers;
defined('ACCESS') or die('no direct access');

/**
 * The module mapper class.
 *
 * @package ilch
 */
class Module extends \Ilch\Mapper
{
    /**
     * Gets all modules.
     *
     * @return array|Admin_ModuleModel[]
     */
    public function getModules()
    {
        $modules = array();
        $modulesRows = $this->db()->select('*')
            ->from('modules')
            ->execute()
            ->fetchRows();

        foreach ($modulesRows as $moduleRow) {
            $moduleModel = new \Admin\Models\Module();
            $moduleModel->setKey($moduleRow['key']);
            $moduleModel->setAuthor($moduleRow['author']);
            $moduleModel->setSystemModule($moduleRow['system']);
            $moduleModel->setIconSmall($moduleRow['icon_small']);
            $contentRows = $this->db()->select('*')
                ->from('modules_content')
                ->where(array('key' => $moduleRow['key']))
                ->execute()
                ->fetchRows();

            foreach ($contentRows as $contentRow) {
                $moduleModel->addContent($contentRow['locale'], array('name' => $contentRow['name'], 'description' => $contentRow['description']));
            }

            $modules[] = $moduleModel;
        }

        return $modules;
    }
    /**
     * Inserts a module model in the database.
     *
     * @param \Admin\Models\Module $module
     */
    public function save(\Admin\Models\Module $module)
    {
        $moduleId = $this->db()->insert('modules')
            ->values(array('key' => $module->getKey(), 'system' => $module->getSystemModule(),
                'icon_small' => $module->getIconSmall(), 'author' => $module->getAuthor()))
            ->execute();

        foreach ($module->getContent() as $key => $value) {
            $this->db()->insert('modules_content')
                ->values(array('key' => $module->getKey(), 'locale' => $key, 'name' => $value['name'], 'description' => $value['description']))
                ->execute();
        }

        return $moduleId;
    }

    /**
     * @param string $key
     */
    public function delete($key)
    {
        $this->db()->delete('modules')
            ->where(array('key' => $key))
            ->execute();
        
        $this->db()->delete('modules_content')
            ->where(array('key' => $key))
            ->execute();
    }
}
