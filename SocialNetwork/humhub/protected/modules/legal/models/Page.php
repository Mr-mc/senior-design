<?php

namespace humhub\modules\legal\models;

use humhub\components\ActiveRecord;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\legal\Module;
use Yii;

/**
 * This is the model class for table "legal_page".
 *
 * @property integer $id
 * @property string $page_key
 * @property string $language
 * @property string $title
 * @property string $content
 * @property integer $last_update
 */
class Page extends ActiveRecord
{
    const PAGE_KEY_IMPRINT = 'imprint';
    const PAGE_KEY_TERMS = 'terms';
    const PAGE_KEY_PRIVACY_PROTECTION = 'privacy';
    const PAGE_KEY_COOKIE_NOTICE = 'cookies';
    const PAGE_KEY_LEGAL_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'legal_page';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (empty($this->title)) {
            $this->title = static::getDefaultPageTitle($this->page_key);
        }

        if (empty($this->content)) {
            if (!$this->isNewRecord) {
                $this->delete();
            }

            return false;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        RichText::postProcess($this->content, $this);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('LegalModule.base', 'Title') . ' (' . $this->language . ')',
            'content' => Yii::t('LegalModule.base', 'Content') . ' (' . $this->language . ')',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'content' => Yii::t('LegalModule.base', 'Will be used as default, if the legal texts are not available in the user‘s language.'),
        ];
    }

    /**
     * @return array the menu locations
     */
    public static function getPages()
    {
        return [
            static::PAGE_KEY_IMPRINT => Yii::t('LegalModule.base', 'Imprint'),
            static::PAGE_KEY_TERMS => Yii::t('LegalModule.base', 'Terms and Conditions'),
            static::PAGE_KEY_PRIVACY_PROTECTION => Yii::t('LegalModule.base', 'Privacy Policy'),
            static::PAGE_KEY_COOKIE_NOTICE => Yii::t('LegalModule.base', 'Cookie notification'),
            static::PAGE_KEY_LEGAL_UPDATE => Yii::t('LegalModule.base', 'Legal Update'),
        ];
    }

    /**
     * @return array list of footer menu pages
     */
    public static function getFooterMenuPages()
    {
        return [static::PAGE_KEY_TERMS, static::PAGE_KEY_PRIVACY_PROTECTION, static::PAGE_KEY_IMPRINT];
    }

    public static function getDefaultPageTitle($pageKey)
    {
        if ($pageKey == 'cookies') {
            // In case of cookies, the title is the button label
            return Yii::t('LegalModule.base', 'Got it!');
        }

        return self::getPages()[$pageKey];
    }

    /**
     * @param string $pageKey
     * @param string $language
     * @return Page|null the page or null
     */
    public static function getPage($pageKey, $language = null)
    {
        if ($language === null) {
            $language = Yii::$app->language;
        }

        $page = Page::findOne(['language' => $language, 'page_key' => $pageKey]);
        if ($page === null) {
            /** @var Module $module */
            $module = Yii::$app->getModule('legal');
            $page = Page::findOne(['language' => $module->getDefaultLanguage(), 'page_key' => $pageKey]);
        }

        return $page;
    }
}