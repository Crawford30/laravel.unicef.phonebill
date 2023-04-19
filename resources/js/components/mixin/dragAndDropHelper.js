
const dragAndDropHelper = {
    data() {
        return {

        }
    },
    methods: {
        stopDrop(e) {
            e.preventDefault();
        },
        fileData(e, type) {
            let app = this;
            let file = null;
            let fileExtension = null;
            if(type === 'browse') {
                let files = e.target.files || e.dataTransfer.files;
                file = files[0];
            }else {
                file = e.originalEvent.dataTransfer.files[0];
            }

            let fileNameArray = file["name"].split(".");
            fileExtension = fileNameArray[fileNameArray.length - 1];

            return {
                "file": file,
                "ext": fileExtension
            };
        },
        dragAndDrop(onFileDropped) {
            let app = this;
            let uploadZone = $("#drop-area");
            uploadZone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            })
                .on('dragover dragenter', function() {  })
                .on('dragleave dragend drop', function() {  })
                .on('drop', function(e) {
                    onFileDropped(e);
                });
        },
    }
};

export default dragAndDropHelper;
