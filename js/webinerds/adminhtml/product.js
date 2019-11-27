Product.Gallery.addMethods({
    updateImage : function(file) {
        var index = this.getIndexByFile(file);
        this.images[index].label = this
            .getFileElement(file, 'cell-label input').value;
        this.images[index].position = this.getFileElement(file,
            'cell-position input').value;
        this.images[index].removed = (this.getFileElement(file,
            'cell-remove input').checked ? 1 : 0);
        this.images[index].page = (this.getFileElement(file,
            'cell-page input').checked ? 1 : 0);
        this.images[index].disabled = (this.getFileElement(file,
            'cell-disable input').checked ? 1 : 0);
        this.getElement('save').value = Object.toJSON(this.images);
        this.updateState(file);
        this.container.setHasChanges();
    },
    updateVisualisation : function(file) {
        var image = this.getImageByFile(file);
        this.getFileElement(file, 'cell-label input').value = image.label;
        this.getFileElement(file, 'cell-position input').value = image.position;
        this.getFileElement(file, 'cell-remove input').checked = (image.removed == 1);
        this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
        this.getFileElement(file, 'cell-page input').checked = (image.page == 1);
        $H(this.imageTypes)
            .each(
            function(pair) {
                if (this.imagesValues[pair.key] == file) {
                    this.getFileElement(file,
                        'cell-' + pair.key + ' input').checked = true;
                }
            }.bind(this));
        this.updateState(file);
    }
});

$j(document).ready(function() {
	var limit = 3;

	$j(document).on('click', '.cell-page input', function(e) {
		var count = $j('.cell-page input:checked').length;

		if (count > limit) {
			e.preventDefault();
			alert('You can select only 3 items');
		}
	});

});
