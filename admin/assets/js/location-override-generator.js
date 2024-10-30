var geoTypeMap = {
  'country':'countryCode',
  'city':'city',
  'state':'stateProv'
};



window.addEventListener("DOMContentLoaded", () => {
  window.locationOverrideGenerator = new LocationOverrideGenerator(
    'ifso_geo_override',
    ".location-override-generator",
    ".result-shortcode",
    ".shortcode-error",
    ".result-css",
    true,
    '.preview-container',
    '.drag-table',
    '.preview-design'
  )

  window.locationOverrideGenerator.initEditor()

  window.dispatchEvent( new Event('generator-ready') )

  window.ifsoLocGenPipe = {
    accept : function (data){
      window.locationOverrideGenerator.handleLocationFinderResult(data);
    }
  };
})


class LocationOverrideGenerator extends ShortcodeGenerator {
  constructor(prefix, formSelector, shortcodeSelector, errorSelector, cssSelector, instantChange, previewContainerSelector, locationsTableSelector, styleElementSelector) {
    super(prefix, formSelector, shortcodeSelector, errorSelector, instantChange)

    this.locationsTableSelector = locationsTableSelector
    this.previewContainerSelector = previewContainerSelector
    this.styleElementSelector = styleElementSelector
    this.previewContainer = document.querySelector(previewContainerSelector)
    this.cssElement = document.querySelector(cssSelector)

    this.locationsTable = new DragTable(this.locationsTableSelector)
    this.locationsTable.table.addEventListener('change', () => { this.submitHandler() })

    this.initTypeOptions()

    this.firstSubmit = true
    
    // add to shortcode generator later
    this.joinOperatorNoFilter = function(cat, vals, separator) {
      return ` ${cat}="${vals.filter(v => v !== '').join(separator)}"`
    }

    this.submitHandler = function(event) {
      let formdata = new FormData(this.formElement)

      this.previewContainer.innerHTML = '' // clear previous preview
      this.shortcodeElement.value = '' // clear previous shortcode
      this.errorElement.innerHTML = '' // clear previous errors
      this.errorElement.classList.remove('active')

      try {
        let shortcode = this.generateShortcode(formdata)
        this.afterSubmitHandler(shortcode)
      } catch (errors) {
        this.errorElement.classList.add('active')
        let errorLabels = errors.map(e => this.createErrorLabel(e))
        errorLabels.forEach(t => this.errorElement.appendChild(t))
        this.afterSubmitErrorHandler(errors)
      }
    }

    this.afterSubmitHandler = function (shortcode) {
      let previewReadyEventName = this.firstSubmit ? 'preview-ready-first' : 'preview-ready'
      this.firstSubmit = false

      let currentPromise = this.renderContent(shortcode).then((resultHTML) => {
        if (currentPromise === this.latestRenderContentPromise) {
          this.previewContainer.innerHTML = resultHTML
          // disable the live preview onchage to prevent accidenatl page reload
          this.previewContainer.querySelectorAll('input,select').forEach(input => input.onchange = undefined)
          
          // add class to elements in the preview that do not have any, to use with the editor
          let markPreviewElements = () => {
            let marks = [
              ['option', 'ifso-preview-select-option'],
              ['input[type="radio"]', 'ifso-preview-radio'],
              ['label', 'ifso-preview-radio-label'],
              ['button[type="submit"]', 'ifso-preview-submit-button']
            ]
            marks.forEach(m => this.previewContainer.querySelectorAll(m[0]).forEach(el => {
              el.classList.add(m[1])
            }))
          }
          markPreviewElements()

          // activate template editor
          window.dispatchEvent( new Event(previewReadyEventName) )
          window.addEventListener('template-editor-ready', () => { this.cssElement.value = templateEditor.getCopyCss() }) // fill css
        }
      }).catch((error) => {
        throw(error)
        if (currentPromise === this.latestRenderContentPromise) {
          this.errorElement.classList.add('active')
          this.errorElement.appendChild(this.createErrorLabel('Unable to generate a live preview, please try again later'))
        }
      }).finally(() => {
        if (currentPromise === this.latestRenderContentPromise) {
          this.shortcodeElement.value = shortcode // fill shortcode
        }
      })
      this.latestRenderContentPromise = currentPromise
    }

    this.afterSubmitErrorHandler = (errors) => {
      this.previewContainer.innerHTML = ''
    }

    this.operators = [
      [
        ["options"], (cat) => {
          let data = this.locationsTable.data
          if ( data.length === 0 ) throw 'Please add locations to generate a shortcode'

          let values = data.map(location => location.loc_val)
          let labels = data.map(location => location.loc_label)
          let result = this.joinOperatorNoFilter(cat, values, ',')
          let has_extra_data = false
          let extra_data = data.map(el=>{if(typeof(el.extra_fields)!=='undefined'){has_extra_data=true;return {fields:el.extra_fields};}return {};});

          if ( labels.some((label, i) => label !== values[i]) ) result += this.joinOperatorNoFilter('labels', labels, ',')
          if ( has_extra_data ) result+= (' extra-data="' + encodeURIComponent(JSON.stringify(extra_data)) + '"')
          return result
        },
      ],
      [
        ["type"], (cat) => {
          let selectedValue = document.querySelector('.type-option.selected').dataset.value
          return this.omitDefault(cat, [selectedValue], 'select')
        },
      ],
      [
        ["default-option"], (cat, vals) => this.omitDefault(cat, vals, "Select"),
      ],
      [
        ["button"], (cat, vals, formData) => {
          if (vals[0] !== "value") return this.omitDefault(cat, vals, '')
          let valueEntry = formData.find(entry => entry[0] === cat + '-value')
          let copy = valueEntry.map(v => v)
          copy.shift()
          return ` ${cat}="${copy.join(' ')}"`
        }
      ],
      [
        ["geo-type"], (cat) => {
          if( typeof(this.locationsTable.data[0]) !== 'undefined' ){
            let geo_cat = this.locationsTable.data[0].loc_type.toLowerCase();
            let sc_cat = geoTypeMap[geo_cat];

            if(this.locationsTable.containsDissonantGeoTypes()){
              jQuery('.dissonant-geo-types-error').show();
              jQuery('.dissonant-geo-types-error .geo-type-to-use').html(geo_cat);
            }
            else jQuery('.dissonant-geo-types-error').hide()

            return ' ' + cat + '="' + sc_cat + '"'
          }
        },
      ],
      [
        ["redirect"], (cat, vals, formData) => {
          if ( vals[0] !== 'on' ) return '' // returns nothing if the checkbox is disabled or unchecked

          let tableData = this.locationsTable.data
          let tableUrls = tableData.map(location => location.loc_url)
          let url = formData.find(entry => entry[0] === cat + '-value')[1]
          let urlList = tableUrls.map(val => !val ? url : val) // values from the table overwrites the field value
          let listEmpty = urlList.every(val => val === '')
          let listUniform = urlList.every(val => val === urlList[0])
          
          if ( listEmpty ) return ''
          if ( listUniform ) return ` redirect ="${urlList[0]}" ajax="yes"`
          return this.joinOperatorNoFilter('redirects', urlList, ',') + ' ajax="yes"'
        }
      ],
      [
        ["ajax-render", "show-flags", "autodetect-location"], (cat, vals) => {
          if ( vals[0] === 'on' ) return ` ${cat}="yes"`
          return ''
        },
      ],
      [
        ['classname'], 
        (cat, vals) => {
          let designElement = document.querySelector(this.styleElementSelector)
          let designName = designElement.dataset.name
          let originalClassname = vals[0]
          return this.joinOperator(cat, [originalClassname, designName], ' ')
        }
      ],
      [
        ["button-value", "redirect-value"], () => ''
      ],
    ]
  }

  initTypeOptions() {
    let form = document.querySelector(this.formSelector)
    let options = form.querySelectorAll('.type-option')
    let orientationField = form.querySelector('fieldset[name="orientation"]')
    let defaultOptionField = form.querySelector('fieldset[name="default-option"]')

    options.forEach(opt => {
        opt.addEventListener('click', (event) => {
            options.forEach(o => o.classList.remove('selected'))
            opt.classList.add('selected')

            let isRadio = opt.dataset.value === 'radio'
            defaultOptionField.disabled = isRadio
            orientationField.disabled = !isRadio

            form.dispatchEvent( new Event('change') )
        })
    })
  }

  processLocationData(dataStr) {
    let data = JSON.parse(dataStr);
    let dataWithLabels = data.map(location => {
      if (location.loc_type === 'COUNTRY')
        location.loc_label = all_countries_opts.find(item => item.value === location.loc_val).display_value
      else
        location.loc_label = location.loc_val
      return location
    })
    return dataWithLabels
  }

  handleLocationFinderResult(dataStr) {
    this.locationsTable.table.innerHTML = ''
    let newData = this.processLocationData(dataStr)
    let oldData = this.locationsTable.data
    this.locationsTable.setData(oldData.concat(newData))
  }



  copyStyle() {
    let style = this.cssElement.value
    navigator.clipboard.writeText(style);
  }

  copyShortcode() {
    let shortcode = this.shortcodeElement.value
    navigator.clipboard.writeText(shortcode);
  }

  copyAll() {
    let shortcode = this.shortcodeElement.value
    let style = this.cssElement.value
    let combined = shortcode + '\n\n' + '<style>' + '\n' + style + '\n' + '</style>'
    navigator.clipboard.writeText(combined);
  }

  initEditor() {
    window.templateEditor = new TemplateEditor('preview-generator', {
      initEvent: {target: window, type: 'generator-ready'},
      loadEvent: {target: window, type: 'preview-ready-first'},
      openEvent: undefined,
      resetEvent: {target: window, type: 'preview-ready'},
    
      customTagNames: ['FORM'],
    
      manualSelection: {
        editables: [
            '.preview-container',
            '.preview-container .ifso_selection_form' ,
            '.preview-container select',
            '.preview-container .ifso-preview-select-option',
            '.preview-container .if-so-add-to-grp-radio-options',
            '.preview-container .ifso-preview-radio',
            '.preview-container .ifso-preview-radio-label',
            '.preview-container .ifso-preview-submit-button',
        ],
        names: [
          'parent',
          'form alignment',
          'select fields',
          'select field options',
          'radio buttons container',
          'radio buttons',
          'radio labels',
          'submit button',
        ], 
        wrapper: undefined,
        parent: undefined,
        scriptElement: undefined,
        styleElement: this.styleElementSelector
      },
    },
    
    [
      {type: 'text', name: 'texts', tags: ['P', 'DIV', 'SPAN', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'DEL', 'INS', 'A', 'BUTTON']}, //, 'VIDEO'
    
      {type: 'attribute', name: 'href', label: 'link', tags: ['A']},
      {type: 'attribute', name: 'target', label: 'target', tags: ['A']},
      {type: 'attribute', name: 'rel', label: 'relation', tags: ['A']},
      {type: 'attribute', name: 'src', tags: ['IMG', 'SOURCE', 'IFRAME']},
      {type: 'attribute', name: 'alt', label: 'alternate text', tags: ['IMG']},
      {type: 'attribute', name: 'onclick', label: 'on click', tags: 'every'},
    
      {type: 'style', name: 'color', tags: ['P', 'SPAN', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'DEL', 'INS', 'A', 'BUTTON', 'DIV', 'OPTION', 'SELECT']},
      {type: 'style', name: 'background-color', label: 'background', tags: ['DIV', 'SPAN', 'A', 'BUTTON', 'svg', 'FORM', 'SELECT', 'OPTION']},
      {type: 'style', name: 'border-color', tags: ['DIV', 'A', 'BUTTON', 'svg', 'SELECT']},
      {type: 'style', name: 'border-top-color', tags: ['DIV', 'A', 'BUTTON', 'svg']},
      {type: 'style', name: 'border-right-color', tags: ['DIV', 'A', 'BUTTON', 'svg']},
      {type: 'style', name: 'border-bottom-color', tags: ['DIV', 'A', 'BUTTON', 'svg']},
      {type: 'style', name: 'border-left-color', tags: ['DIV', 'A', 'BUTTON', 'svg']},
      {type: 'style', name: 'stroke', tags: ['svg']},
      {type: 'style', name: 'fill', tags: ['svg']},
    
      {type: 'style', name: 'font-size', tags: ['P', 'SPAN', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'DEL', 'INS', 'A', 'BUTTON', 'OPTION', 'LABEL', 'SELECT', 'INPUT']},
      {type: 'style', name: 'font-weight', tags: ['P', 'SPAN', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'DEL', 'INS', 'A', 'BUTTON', 'OPTION', 'LABEL', 'SELECT', 'INPUT']},
      {type: 'style', name: 'font-family', tags: ['P', 'SPAN', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'DEL', 'INS', 'A', 'BUTTON', 'OPTION', 'LABEL', 'SELECT', 'INPUT']},
    
    
      {type: 'style', name: 'border-width', tags: 'every'},
      {type: 'style', name: 'border-style', tags: 'every'},
      {type: 'style', name: 'border-top-style', tags: 'every'},
      {type: 'style', name: 'border-right-style', tags: 'every'},
      {type: 'style', name: 'border-bottom-style', tags: 'every'},
      {type: 'style', name: 'border-left-style', tags: 'every'},
      {type: 'style', name: 'border-radius', tags: 'every'},

      {type: 'style', name: 'margin-top', tags: 'every'},
      {type: 'style', name: 'margin-bottom', tags: 'every'},
      {type: 'style', name: 'margin-right', tags: 'BUTTON'},
      {type: 'style', name: 'margin-left', tags: 'BUTTON'},
      
      {type: 'style', name: 'text-align', tags: 'every'},
    ])
  }
}
