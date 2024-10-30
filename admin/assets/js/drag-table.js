class DragTable {
  constructor(tableSelector, initData = []) {
    this.table = document.querySelector(tableSelector)
    this.setData(initData)
    this.createSortable()
  }

  setData(data) {
    this.data = this.formatData(data)
    this.populateTable()
    this.table.dispatchEvent( new Event('change') )
  }

  formatData(data) {
    return data.map(loc => {
      if ( !loc.loc_url ) loc.loc_url = ''
      return loc
    })
  } 

  populateTable() {
    this.table.innerHTML = ''
    this.data.forEach(location => {
      this.table.appendChild(this.createLocationItem(location))
    })
  }

  createLocationItem(location) {
    let item = document.createElement('div')
    item.className = 'location'
    item.innerHTML = `
      <span class="location-handle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M352.201 425.775l-79.196 79.196c-9.373 9.373-24.568 9.373-33.941 0l-79.196-79.196c-15.119-15.119-4.411-40.971 16.971-40.97h51.162L228 284H127.196v51.162c0 21.382-25.851 32.09-40.971 16.971L7.029 272.937c-9.373-9.373-9.373-24.569 0-33.941L86.225 159.8c15.119-15.119 40.971-4.411 40.971 16.971V228H228V127.196h-51.23c-21.382 0-32.09-25.851-16.971-40.971l79.196-79.196c9.373-9.373 24.568-9.373 33.941 0l79.196 79.196c15.119 15.119 4.411 40.971-16.971 40.971h-51.162V228h100.804v-51.162c0-21.382 25.851-32.09 40.97-16.971l79.196 79.196c9.373 9.373 9.373 24.569 0 33.941L425.773 352.2c-15.119 15.119-40.971 4.411-40.97-16.971V284H284v100.804h51.23c21.382 0 32.09 25.851 16.971 40.971z"/></svg>
      </span>
      <span class="location-value-container location-container">
        <span class="location-value">${location.loc_label}</span>
      </span>
      <span class="location-label-container location-container">
        <input type="text" class="location-label" value="${location.loc_label}">
      </span>
      <span class="location-url-container location-container">
        <input type="text" class="location-url" value="${location.loc_url}">
      </span>
      <span class="location-remove">
        <svg viewBox="0 0 10 10"><path d="M1.5 1.5 L8.5 8.5 M1.5 8.5 L8.5 1.5"></path></svg>
      </span>
    `
    
    item.querySelector('.location-label').addEventListener('change', (event) => this.onLocationLabelChange(event,) )
    item.querySelector('.location-url').addEventListener('change', (event) => this.onLocationUrlChange(event) )

    return item
  }

  createSortable() {
    this.sortable = Sortable.create(this.table, {
      animation: 150,
      handle: ".location-handle",
      filter: ".location-remove",
      onFilter: (event) => {
        if (Sortable.utils.is(event.target, ".location-remove")) {
          event.item.parentNode.removeChild(event.item)
          this.data.splice(event.oldIndex, 1)
          this.table.dispatchEvent( new Event('change') )
        }
      },
      onEnd: (event) => {
        this.moveLocation(event.oldIndex, event.newIndex)
      },
    })
  }

  onLocationLabelChange(event) {
    let locationItem = event.target.parentNode.parentNode
    let locationIndex = Array.from(locationItem.parentNode.children).indexOf(locationItem);
    this.data[locationIndex].loc_label = event.target.value
  }

  onLocationUrlChange(event) {
    let locationItem = event.target.parentNode.parentNode
    let locationIndex = Array.from(locationItem.parentNode.children).indexOf(locationItem);
    this.data[locationIndex].loc_url = event.target.value
  }
  
  moveLocation(oldInedx, newIndex) {
    arrayMove(this.data, oldInedx, newIndex)
    this.table.dispatchEvent( new Event('change') )

    function arrayMove(arr, oldInedx, newIndex) {
      while (oldInedx < 0) { oldInedx += arr.length }
      while (newIndex < 0) { newIndex += arr.length }
      if (newIndex >= arr.length) {
        var k = newIndex - arr.length + 1
        while (k--) { arr.push(undefined) }
      }
      arr.splice(newIndex, 0, arr.splice(oldInedx, 1)[0])
      return arr
    }
  }

  containsDissonantGeoTypes(){
    var firstType = null;
    for (var el of this.data){
      if(firstType===null)
        firstType = el.loc_type;
      else if(firstType!==el.loc_type)
        return true;
    }
    return false;
  }
}