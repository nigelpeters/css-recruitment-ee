if (typeof(EditorButtons) == 'undefined') var EditorButtons = {};
EditorButtons.Link = {};
EditorButtons.PastePlainText = {};
EditorButtons.Templates = {};
EditorButtons.Styles = {};

if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.dd_keyboard_shortcuts = {

    init: function()
    {
        this.$editor.parent().on('keydown', $.proxy(function(e) {

            //if (rtePaste) return false;

            var key = e.which;
            var ctrl = e.ctrlKey || e.metaKey;

            //CTRL + D
            if (ctrl && key === 68) {
                e.preventDefault();
                this.toggle();
                return false;
            }

        }, this));
    }

};

//********************************************************************************* //

EditorButtons.Link.OpenModal = function(buttonName, buttonDOM, buttonObject){
	var redactor = this;
	redactor.selectionSave();

	var endCallback = function(url)
	{
		$('#redactor_link_url').focus();

		$('#redactor_site_page').change(function(){
			if (! $('#redactor_site_page_text').data('filled')) {
				$('#redactor_site_page_text').attr('value', $("#redactor_site_page").children("option").filter(":selected").text() );
			}
		}).trigger('change');
	};

	var ModalContent = EditorButtons.Link.ModalContent(redactor);

	redactor.modalInit(redactor.opts.curLang.link, ModalContent, 460, function(){

		redactor.insert_link_node = false;

		var sel = redactor.getSelection();
		var url = '', text = '', target = '';

		var elem = redactor.getParent();
		var par = $(elem).parent().get(0);
		if (par && par.tagName === 'A')
		{
			elem = par;
		}

		if (elem && elem.tagName === 'A')
		{
			url = elem.href;
			text = $(elem).text();
			target = elem.target;

			redactor.insert_link_node = elem;
		}
		else text = sel.toString();

		$('.redactor_link_text').val(text);

		var thref = self.location.href.replace(/\/$/i, '');
		var turl = url.replace(thref, '');

		if (url.search('mailto:') === 0)
		{
			redactor.modalSetTab.call(redactor, 4);

			$('#redactor_tab_selected').val(4);
			$('#redactor_link_mailto').val(url.replace('mailto:', ''));
		}
		else if (turl.search(/^#/gi) === 0)
		{
			redactor.modalSetTab.call(redactor, 5);

			$('#redactor_tab_selected').val(5);
			$('#redactor_link_anchor').val(turl.replace(/^#/gi, ''));
		}
		else if ( $('#redactor_navee').find("option[value='"+url+"']").length > 0 )
		{
			redactor.modalSetTab.call(redactor, 3);

			$('#redactor_tab_selected').val(3);
			$('#redactor_navee').val(url);
		}
		else if ( $('#redactor_site_page').find("option[value='"+url+"']").length > 0 )
		{
			redactor.modalSetTab.call(redactor, 2);

			$('#redactor_tab_selected').val(2);
			$('#redactor_site_page').val(url);
		}
		else
		{
			$('#redactor_link_url').val(url);
		}

		if (target === '_blank')
		{
			$('.redactor_link_blank').attr('checked', true);
		}

		$('#redactor_insert_link_btn').click(function(ee){
			EditorButtons.Link.InsertLink(redactor, ee);
		});

		$('#redactor_modal').find('.redactor_tab:visible').find('.redactor_input:first').focus();
	});
};

//********************************************************************************* //

EditorButtons.Link.ModalContent = function(redactor){

	var H = [];

		var ModalContent = '<div id="redactor_modal_content">' +
				'<div id="redactor_modal_inner">' +
					'<section>'+
					'<textarea id="redactor_paste_plaintext_area" style="height:400px"></textarea>'+
					'</section>'+
					'<footer>' +
						'<a href="javascript:void(null);" class="redactor_modal_btn redactor_btn_modal_close">' + redactor.opts.curLang.cancel + '</a>' +
						'<input type="button" class="redactor_modal_btn" id="redactor_save_plain_text" value="' + redactor.opts.curLang.insert + '" />' +
					'</footer>' +
				'</div>';

	H.push('<div id="redactor_modal_content">');
	H.push('<div id="redactor_modal_inner">');

	H.push('<section>');
	H.push('<form id="redactorInsertLinkForm" method="post" action="">');
		H.push('<div id="redactor_tabs">');
			H.push('<a href="javascript:void(null);" class="redactor_tabs_act">URL</a>');

			if (EditorButtons.site_pages) H.push('<a href="javascript:void(null);">Site Page</a>');
			else {
				EditorButtons.site_pages = '';
				H.push('<a href="javascript:void(null);" style="display:none;">Site Page</a>');
			}

			if (EditorButtons.navee) H.push('<a href="javascript:void(null);">NavEE</a>');
			else {
				EditorButtons.navee = '';
				H.push('<a href="javascript:void(null);" style="display:none;">NavEE</a>');
			}

			H.push('<a href="javascript:void(null);">Email</a>');
			H.push('<a href="javascript:void(null);">' + redactor.opts.curLang.anchor + '</a>');
		H.push('</div>');
			H.push('<input type="hidden" id="redactor_tab_selected" value="1" />');

			H.push('<div class="redactor_tab" id="redactor_tab1">' +
						'<label>URL</label><input type="text" id="redactor_link_url" class="redactor_input"  />' +
						'<label>' + redactor.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_url_text" />' +
						'<label><input type="checkbox" class="redactor_link_blank" id="redactor_wwwink_blank"> ' + redactor.opts.curLang.link_new_tab +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab2" style="display: none;">' +
						'<label>Site Page</label><select id="redactor_site_page" style="width:100%">' + EditorButtons.site_pages + '</select>' +
						'<label>' + redactor.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_site_page_text" />' +
						'<label><input type="checkbox" class="redactor_link_blank" id="redactor_site_page_blank"> ' + redactor.opts.curLang.link_new_tab +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab3" style="display: none;">' +
						'<label>Link Item</label><select id="redactor_navee" style="width:100%">' + EditorButtons.navee + '</select>' +
						'<label>' + redactor.opts.curLang.text + '</label><input type="text"  class="redactor_input redactor_link_text" id="redactor_site_page_text" />' +
						'<label><input type="checkbox" class="redactor_link_blank" id="redactor_navee_blank"> ' + redactor.opts.curLang.link_new_tab +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab4" style="display: none;">' +
						'<label>Email</label><input type="text" id="redactor_link_mailto" class="redactor_input" />' +
						'<label>' + redactor.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_mailto_text" />' +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab5" style="display: none;">' +
						'<label>' + redactor.opts.curLang.anchor + '</label><input type="text" class="redactor_input" id="redactor_link_anchor"  />' +
						'<label>' + redactor.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_anchor_text" />' +
					'</div>');

		H.push('</form>');
		H.push('</section>');

		H.push('<footer>');
			H.push('<a href="javascript:void(null);" class="redactor_modal_btn redactor_btn_modal_close">' + redactor.opts.curLang.cancel + '</a>');
			H.push('<input type="button" class="redactor_modal_btn" id="redactor_insert_link_btn" value="' + redactor.opts.curLang.insert + '" />');
		H.push('</footer>');

		H.push('</div>');
		H.push('</div>');

	return H.join('');
};

//********************************************************************************* //

EditorButtons.Link.InsertLink = function(redactor, e){
	redactor.selectionRestore();

	var tab_selected = $('#redactor_tab_selected').val();
	var link = '', text = '', target = '';
	var re;

	if (tab_selected === '1') // url
	{
		link = $('#redactor_link_url').val();
		text = $('#redactor_link_url_text').val();

		if ($('#redactor_wwwink_blank').attr('checked'))
		{
			target = '_blank';
		}

		// test url (add protocol)
		var pattern = '((xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}';
		re = new RegExp('^(http|ftp|https)://' + pattern, 'i');
		var re2 = new RegExp('^' + pattern, 'i');

		if (link.search(re) == -1 && link.search(re2) == 0 && redactor.opts.linkProtocol)
		{
			link = redactor.opts.linkProtocol + link;
		}
	}
	else if (tab_selected === '2') // site pages
	{
		link = $('#redactor_site_page').val();
		text = $('#redactor_site_page_text').val();

		if ($('#redactor_site_page_blank').attr('checked'))
		{
			target = '_blank';
		}

		// test http
		re = new RegExp('^https?://', 'i');
		if (link.search(re) == -1)
		{
			link = redactor.opts.protocol + link;
		}
	}
	else if (tab_selected === '4') // mailto
	{
		link = 'mailto:' + $('#redactor_link_mailto').val();
		text = $('#redactor_link_mailto_text').val();
	}
	else if (tab_selected === '5') // anchor
	{
		link = '#' + $('#redactor_link_anchor').val();
		text = $('#redactor_link_anchor_text').val();
	}

	redactor.linkInsert('<a href="' + link + '" target="' + target + '">' +  text + '</a>', $.trim(text), link, target);
	redactor.selectionRemove();
	redactor.selectionRemoveMarkers();
};

//********************************************************************************* //


EditorButtons.PastePlainText.OpenModal = function(buttonName, buttonDOM, buttonObject){
	var redactor = this;
	redactor.selectionSave();

	var ModalContent = '<div id="redactor_modal_content">' +
				'<div id="redactor_modal_inner">' +
					'<section>'+
					'<textarea id="redactor_paste_plaintext_area" style="height:400px"></textarea>'+
					'</section>'+
					'<footer>' +
						'<a href="javascript:void(null);" class="redactor_modal_btn redactor_btn_modal_close">' + redactor.opts.curLang.cancel + '</a>' +
						'<input type="button" class="redactor_modal_btn" id="redactor_save_plain_text" value="' + redactor.opts.curLang.insert + '" />' +
					'</footer>' +
				'</div>';

	// title, content, width, callback
	redactor.modalInit('Paste Plain Text', ModalContent, 600, function(){

		$('#redactor_paste_plaintext_area').bind('paste', function(e){
			EditorButtons.PastePlainText.ProcessPaste(e, redactor);
		});

		$('#redactor_save_plain_text').bind('click', function(e){
			EditorButtons.PastePlainText.SavePaste(e, redactor);
		});

		$('#redactor_save_plain_text')[0].focus();

	});
};

//********************************************************************************* //

EditorButtons.PastePlainText.ProcessPaste = function(e, redactor){
	var el = $(e.target);

};

//********************************************************************************* //

EditorButtons.PastePlainText.SavePaste = function(e, redactor){
	var content = $('#redactor_paste_plaintext_area').val();

	content = redactor.cleanParagraphy(content);

	//
	redactor.insertHtml(content);
	redactor.sync();
	redactor.modalClose();
	redactor.selectionRestore();

	//Editor.TriggerRedactorCleanup(redactor);
};

//********************************************************************************* //

EditorButtons.NoSettingsAlert = function(redactor, e, btnkey){
    alert('No button settings have been defined');
};

// ********************************************************************************* //

EditorButtons.Templates.InsertTemplate = function(dropdownBtnName, item, btnObject){
    if (typeof(Editor.settings) == 'undefined') return;
    if (typeof(Editor.settings.templates) == 'undefined') return;
    if (typeof(Editor.settings.templates.templates) == 'undefined') return;
    if (typeof(Editor.settings.templates.templates[dropdownBtnName]) == 'undefined') return;

    var html = Editor.settings.templates.templates[dropdownBtnName].html;

    var redactor = this;
    redactor.selectionRestore();
	redactor.insertHtmlAdvanced(html);
    redactor.sync();
};

// ********************************************************************************* //


EditorButtons.Styles.OpenDropDown = function(buttonName, buttonDOM, buttonObject, e){
	var redactor = this;
    if (typeof(Editor.settings) == 'undefined') return;
    if (typeof(Editor.settings.styles) == 'undefined') return;
    if (typeof(Editor.settings.styles.styles) == 'undefined') return;

    var dropdown = $(buttonDOM.closest('.redactor_toolbar'));
    var item;

    if (dropdown.find('.editor-dropdown_styles').length == 0) {
        var Styles = Editor.settings.styles.styles;

        dropdown = $('<div class="redactor_dropdown redactor_dropdown_box_styles editor-dropdown_styles" style="display:none;"></div>');

        for (var i = 0; i < Styles.length; i++) {
            item = $('<a class="" style="' + Styles[i].btn_style + '" class="'+Styles[i].btn_class+'" href="javascript:void(null);" data-key="'+i+'"><span class="ind"></span>'+Styles[i].title+'</a>');
            item.click(function(ee){
				EditorButtons.Styles.TriggerItem(redactor, ee);
			});
            dropdown.append(item);
        }

        $(buttonDOM).parent().before(dropdown);
    } else {
    	dropdown = dropdown.find('.editor-dropdown_styles');
    }

    redactor.dropdownShow(e, buttonName);
    return;
};

// ********************************************************************************* //

EditorButtons.Styles.TriggerItem = function(redactor, e){
    // Save the current state
    redactor.bufferSet();


    if (redactor.getCurrent().className == 'redactor_box') {
        return;
    }

    if (redactor.getCurrent().tagName.toLowerCase() == 'body') {
        return;
    }


    var key = e.target.getAttribute('data-key');
    var tmpl = Editor.settings.styles.styles[key];
    var elem, temp;

    // Lets parse the attributes
    var attributes = {};
    if (tmpl.attr){
        temp = $('<div '+tmpl.attr+'>');

        for (var i = temp[0].attributes.length - 1; i >= 0; i--) {
            attributes[temp[0].attributes[i].name] = temp[0].attributes[i].value;
        };

    }

    tmpl.attr_obj = attributes;

    var selection = redactor.getSelectionHtml();
    var selection_obj = $(selection);

    // If empty string
    if (!selection) {
        tmpl.type = 'current';
    }

    if (selection_obj.length > 0) {
        alert('Applying styles to multiple paragraph is not supported');
        return;
    }

    if (tmpl.type == 'inline') {
        EditorButtons.Styles.Exec_Block_Inline('inline', redactor, tmpl, selection);
    }

    else if (tmpl.type == 'block') {
        EditorButtons.Styles.Exec_Block_Inline('block', redactor, tmpl, selection);
    }

    else if (tmpl.type == 'custom') {
        EditorButtons.Styles.Exec_Block_Inline('custom', redactor, tmpl, selection);
    }

    else if (tmpl.type == 'current') {
        elem = $(redactor.getCurrent());
        elem.attr('style', tmpl.styles);
        elem.attr(tmpl.attr_obj);
        elem.addClass(tmpl.classes);
        Editor.TriggerRedactorCleanup(redactor);
    }
};

// ********************************************************************************* //

EditorButtons.Styles.Exec_Block_Inline = function(type, redactor, tmpl, selection){
    var elem = $(redactor.getCurrent());
    var temp;

    if (type == 'inline') {
    	temp = $('<div><span></span></div>').find('span');
    } else if (type == 'block') {
    	temp = $('<div><div></div></div>').find('div');
    } else if (type == 'custom') {
    	temp = $('<div><'+tmpl.custom_type+'></'+tmpl.custom_type+'></div>').find(tmpl.custom_type);
    } else {
    	temp = $('<div><span></span></div>').find('span');
    }

    var temp = temp.attr('style', tmpl.styles).attr('class', tmpl.classes).attr(tmpl.attr_obj).html(selection).parent().html();
    elem.html(elem.html().replace(selection, temp) );
    Editor.TriggerRedactorCleanup(redactor);
};

// ********************************************************************************* //















if (!RedactorPlugins) var RedactorPlugins = {};

RedactorPlugins.fontcolor = {
	init: function()
	{
		var colors = ['#ffffff', '#000000', '#eeece1', '#1f497d', '#4f81bd', '#c0504d', '#9bbb59', '#8064a2', '#4bacc6', '#f79646', '#ffff00', '#f2f2f2', '#7f7f7f', '#ddd9c3', '#c6d9f0', '#dbe5f1', '#f2dcdb', '#ebf1dd', '#e5e0ec', '#dbeef3', '#fdeada', '#fff2ca', '#d8d8d8', '#595959', '#c4bd97', '#8db3e2', '#b8cce4', '#e5b9b7', '#d7e3bc', '#ccc1d9', '#b7dde8', '#fbd5b5', '#ffe694', '#bfbfbf', '#3f3f3f', '#938953', '#548dd4', '#95b3d7', '#d99694', '#c3d69b', '#b2a2c7', '#b7dde8', '#fac08f', '#f2c314', '#a5a5a5', '#262626', '#494429', '#17365d', '#366092', '#953734', '#76923c', '#5f497a', '#92cddc', '#e36c09', '#c09100', '#7f7f7f', '#0c0c0c', '#1d1b10', '#0f243e', '#244061', '#632423', '#4f6128', '#3f3151', '#31859b', '#974806', '#7f6000'];
		var buttons = ['fontcolor', 'backcolor'];

		this.buttonAddSeparator();

		for (var i = 0; i < 2; i++)
		{
			var name = buttons[i];

			var $dropdown = $('<div class="redactor_dropdown redactor_dropdown_box_' + name + '" style="display: none; width: 210px;">');

			this.pickerBuild($dropdown, name, colors);
			$(this.$toolbar).append($dropdown);

			this.buttonAdd(name, this.opts.curLang[name], $.proxy(function(btnName, $button, btnObject, e)
			{
				this.dropdownShow(e, btnName);

			}, this));
		}
	},
	pickerBuild: function($dropdown, name, colors)
	{
		var rule = 'color';
		if (name === 'backcolor') rule = 'background-color';

		var _self = this;
		var onSwatch = function(e)
		{
			e.preventDefault();

			var $this = $(this);
			_self.pickerSet($this.data('rule'), $this.attr('rel'));

		}

		var len = colors.length;
		for (var z = 0; z < len; z++)
		{
			var color = colors[z];

			var $swatch = $('<a rel="' + color + '" data-rule="' + rule +'" href="#" style="float: left; font-size: 0; border: 2px solid #fff; padding: 0; margin: 0; width: 15px; height: 15px;"></a>');
			$swatch.css('background-color', color);
			$dropdown.append($swatch);
			$swatch.on('click', onSwatch);
		}

		var $elNone = $('<a href="#" style="display: block; clear: both; padding: 4px 0; font-size: 11px; line-height: 1;"></a>')
		.html(this.opts.curLang.none)
		.on('click', function(e)
		{
			e.preventDefault();
			_self.pickerSet(rule, false);
		});

		$dropdown.append($elNone);
	},
	pickerSet: function(rule, type)
	{
		this.bufferSet();

		this.$editor.focus();
		this.inlineRemoveStyle(rule);
		if (type !== false) this.inlineSetStyle(rule, type);
		if (this.opts.air) this.$air.fadeOut(100);
		this.sync();
	}
};