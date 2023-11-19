document.querySelectorAll('.drag_drop').forEach (element => {
    element.ondragstart = function () {
      event.dataTransfer.setData('text/plain', event.target.id);
    };
    
    element.ondragover = function () {
      event.preventDefault();
      this.style.borderTop = '3px solid';
    };

element.ondragleave = function () {
this.style.borderTop = "";
};

    element.ondrop = function () {
      event.preventDefault();
      let id = event.dataTransfer.getData('text');
      let element_drag = document.getElementById(id);
      this.parentNode.insertBefore(element_drag, this);
      this.style.borderTop = '';
    };
  });

