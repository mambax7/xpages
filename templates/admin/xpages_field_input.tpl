<{* xPages — Single extra-field input partial.
    Rendered for each custom page field in the "Extra fields" tab.

    Descriptor shape ($field):
      id, type, name, input_id, label, required (bool), desc, value
      options           (list of {value, label, selected, radio_id})  — select/radio
      placeholder       (select only)
      checked           (checkbox only, bool)
      file_input_name   (file only — name="extra_files[N]")
      file_input_id     (file only)
      has_current_file  (file only, bool)
      current_file_url  (file only, pre-sanitised URL)
      current_file_raw  (file only, raw label for <a> text)
      current_file_safe (file only, escaped for hidden input value)
      is_image          (file only, bool)
      labels            {current_file, replace_note, file_none}  — for file type *}>

<div class="xpages-field" id="field-<{$field.id}>">
    <label for="<{$field.input_id}>">
        <{$field.label|escape}>
        <{if $field.required}> <span class="req">*</span><{/if}>
    </label>

    <{if $field.type == 'text' || $field.type == 'email' || $field.type == 'url' || $field.type == 'tel'}>
        <input type="<{$field.type}>" name="<{$field.name}>" id="<{$field.input_id}>" value="<{$field.value|escape}>"<{if $field.required}> required<{/if}>>

    <{elseif $field.type == 'number'}>
        <input type="number" name="<{$field.name}>" id="<{$field.input_id}>" value="<{$field.value|escape}>"<{if $field.required}> required<{/if}>>

    <{elseif $field.type == 'textarea'}>
        <textarea name="<{$field.name}>" id="<{$field.input_id}>" rows="5"<{if $field.required}> required<{/if}>><{$field.value|escape}></textarea>

    <{elseif $field.type == 'checkbox'}>
        <input type="hidden" name="<{$field.name}>" value="0">
        <input type="checkbox" name="<{$field.name}>" id="<{$field.input_id}>" value="1"<{if $field.checked}> checked<{/if}><{if $field.required}> required<{/if}>>

    <{elseif $field.type == 'select'}>
        <select name="<{$field.name}>" id="<{$field.input_id}>"<{if $field.required}> required<{/if}>>
            <option value=""><{$field.placeholder}></option>
            <{foreach item=opt from=$field.options}>
                <option value="<{$opt.value|escape}>"<{if $opt.selected}> selected<{/if}>><{$opt.label|escape}></option>
            <{/foreach}>
        </select>

    <{elseif $field.type == 'radio'}>
        <div class="xpages-radio-group">
            <{foreach item=opt from=$field.options}>
                <label for="<{$opt.radio_id}>">
                    <input type="radio" name="<{$field.name}>" id="<{$opt.radio_id}>" value="<{$opt.value|escape}>"<{if $opt.selected}> checked<{/if}><{if $field.required}> required<{/if}>>
                    <{$opt.label|escape}>
                </label>
            <{/foreach}>
        </div>

    <{elseif $field.type == 'file'}>
        <div class="xpages-file-field">
            <input type="file" name="<{$field.file_input_name}>" id="<{$field.file_input_id}>" accept="image/*,application/pdf,.doc,.docx,.zip">
            <{if $field.has_current_file}>
                <div class="xpages-current-file xp-margin-top-8">
                    <small>
                        <strong><{$field.labels.current_file}></strong><br>
                        <{if $field.is_image}>
                            <img src="<{$field.current_file_url|escape}>" class="xpf-preview--md" alt="">
                        <{else}>
                            <a href="<{$field.current_file_url|escape}>" target="_blank" rel="noopener">📎 <{$field.current_file_raw|escape}></a>
                        <{/if}>
                        <br><span class="xp-text-muted xp-text-small"><{$field.labels.replace_note}></span>
                    </small>
                </div>
                <input type="hidden" name="<{$field.name}>" value="<{$field.current_file_safe|escape}>">
            <{else}>
                <small class="xpf-desc xpf-block-note"><{$field.labels.file_none}></small>
            <{/if}>
        </div>

    <{else}>
        <input type="text" name="<{$field.name}>" id="<{$field.input_id}>" value="<{$field.value|escape}>"<{if $field.required}> required<{/if}>>
    <{/if}>

    <{if $field.desc}>
        <small class="xpf-desc"><{$field.desc|escape}></small>
    <{/if}>
</div>
