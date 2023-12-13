<?php

namespace Botble\Setting\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFolder;
use Botble\Setting\Http\Requests\MediaSettingRequest;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class MediaSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScriptsDirectly('vendor/core/core/setting/js/media.js');

        $folders = MediaFolder::query()->where('parent_id', 0)->pluck('name', 'id')->all();
        $folderIds = json_decode((string)setting('media_folders_can_add_watermark'), true);

        $this
            ->setUrl(route('settings.media.update'))
            ->setActionButtons(view('core/setting::partials.media.action-buttons', ['form' => $this->getFormOption('id')])->render())
            ->setValidatorClass(MediaSettingRequest::class)
            ->contentOnly()
            ->columns(6)
            ->add('media_driver', 'html', [
                'html' => view('core/setting::partials.media.media-driver-field')->render(),
                'colspan' => 6,
            ])
            ->add('media_turn_off_automatic_url_translation_into_latin', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.media.turn_off_automatic_url_translation_into_latin'),
                'value' => RvMedia::turnOffAutomaticUrlTranslationIntoLatin(),
                'colspan' => 6,
            ])
            ->add('media_default_placeholder_image', MediaImageField::class, [
                'label' => trans('core/setting::setting.media.default_placeholder_image'),
                'value' => setting('media_default_placeholder_image'),
                'colspan' => 6,
            ])
            ->add('max_upload_filesize', 'number', [
                'label' => trans('core/setting::setting.media.max_upload_filesize'),
                'value' => setting('max_upload_filesize'),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.max_upload_filesize_placeholder', [
                        'size' => ($maxSize = BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize())),
                    ]),
                    'step' => 0.01,
                ],
                'help_block' => [
                    'text' => trans('core/setting::setting.media.max_upload_filesize_helper', ['size' => $maxSize]),
                ],
                'colspan' => 6,
            ])
            ->add('chunk_size_upload_file', 'html', [
                'html' => view('core/setting::partials.media.chunk-size-upload-field')->render(),
                'colspan' => 6,
            ])
            ->add('media_watermark_warning', 'html', [
                'html' => Blade::render(sprintf('<x-core::alert type="warning">%s</x-core::alert>', trans('core/setting::setting.watermark_description'))),
                'colspan' => 6,
            ])
            ->add('media_watermark_enabled', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.media.enable_watermark'),
                'value' => setting('media_watermark_enabled'),
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '.media-watermark-settings',
                ],
                'colspan' => 6,
            ])
            ->add('open_media_watermark_settings', 'html', [
                'html' => sprintf(
                    '<fieldset class="col-lg-12 row form-fieldset media-watermark-settings" style="display: %s;" data-bb-value="1">',
                    setting('media_watermark_enabled', true) ? 'flex' : 'none'
                ),
            ])
            ->add('media_folders_can_add_watermark_field', 'html', [
                'html' => view('core/setting::partials.media.media-folders-can-add-watermark-field', compact('folders', 'folderIds'))->render(),
            ])
            ->add('media_watermark_source', MediaImageField::class, [
                'label' => trans('core/setting::setting.media.watermark_source'),
                'value' => setting('media_watermark_source'),
                'colspan' => 6,
            ])
            ->add('media_watermark_size', 'number', [
                'label' => trans('core/setting::setting.media.watermark_size'),
                'value' => setting('media_watermark_size', RvMedia::getConfig('watermark.size')),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_size_placeholder'),
                ],
                'colspan' => 3,
            ])
            ->add('watermark_opacity', 'number', [
                'label' => trans('core/setting::setting.media.watermark_opacity'),
                'value' => setting('watermark_opacity', RvMedia::getConfig('watermark.opacity')),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_opacity_placeholder'),
                ],
                'colspan' => 3,
            ])
            ->add('media_watermark_position', SelectField::class, [
                'label' => trans('core/setting::setting.media.watermark_position'),
                'selected' => setting('media_watermark_position', RvMedia::getConfig('watermark.position')),
                'choices' => [
                    'top-left' => trans('core/setting::setting.media.watermark_position_top_left'),
                    'top-right' => trans('core/setting::setting.media.watermark_position_top_right'),
                    'bottom-left' => trans('core/setting::setting.media.watermark_position_bottom_left'),
                    'bottom-right' => trans('core/setting::setting.media.watermark_position_bottom_right'),
                    'center' => trans('core/setting::setting.media.watermark_position_center'),
                ],
                'colspan' => 2,
            ])
            ->add('watermark_position_x', 'number', [
                'label' => trans('core/setting::setting.media.watermark_position_x'),
                'value' => setting('watermark_position_x', RvMedia::getConfig('watermark.x')),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_position_x'),
                ],
                'colspan' => 2,
            ])
            ->add('watermark_position_y', 'number', [
                'label' => trans('core/setting::setting.media.watermark_position_y'),
                'value' => setting('watermark_position_y', RvMedia::getConfig('watermark.y')),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_position_y'),
                ],
                'colspan' => 2,
            ])
            ->add('close_media_watermark_settings', 'html', [
                'html' => '</fieldset>',
            ])
            ->add('media_image_processing_library', 'customRadio', [
                'label' => trans('core/setting::setting.admin_appearance.form.rich_editor'),
                'value' => RvMedia::getImageProcessingLibrary(),
                'values' => array_merge(
                    ['gd' => 'GD Library'],
                    extension_loaded('imagick')
                        ? ['imagick' => 'Imagick', ]
                        : [],
                ),
                'colspan' => 6,
            ])
            ->add('title_media_size', 'html', [
                'html' => '<h4>' . trans('core/setting::setting.media.sizes') . ':</h4>',
                'colspan' => 6,
            ]);

        foreach (RvMedia::getSizes() as $name => $size) {
            $sizeExploded = explode('x', $size);

            $this->add(
                sprintf('media_size_%s_label', $name),
                'html',
                [
                'html' => Blade::render(
                    sprintf(
                        '<x-core::form.label>%s</x-core::form.label>',
                        str_replace('-', ' ', Str::title(Str::slug($name))) . sprintf(
                            '<small> %s </small>',
                            trans('core/setting::setting.media.default_size_value', ['size' => RvMedia::getConfig('sizes.' . $name)])
                        )
                    )
                ),
                    'colspan' => 6,
                ],
            );

            if (count($sizeExploded)) {
                $this->add($nameWidth = sprintf('media_sizes_%s_width', $name), 'number', [
                    'label' => false,
                    'value' => setting($nameWidth, $sizeExploded[0]),
                    'attr' => [
                        'placeholder' => 0,
                    ],
                    'colspan' => 3,
                ])
                ->add($nameHeight = sprintf('media_sizes_%s_height', $name), 'number', [
                    'label' => false,
                    'value' => setting($nameHeight, $sizeExploded[1]),
                    'attr' => [
                        'placeholder' => 0,
                    ],
                    'colspan' => 3,
                ]);
            }
        }

        $this->add('media_sizes_helper', 'html', [
            'html' => Blade::render(sprintf("<x-core::alert title='%s' />", trans('core/setting::setting.media.media_sizes_helper'))),
            'colspan' => 6,
        ]);
    }
}
