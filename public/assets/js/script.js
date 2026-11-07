document.addEventListener("DOMContentLoaded", function () {
  // Initialize form elements
  const categorySelect = document.getElementById("category");
  const otherCategoryGroup = document.getElementById("otherCategoryGroup");
  const reportForm = document.getElementById("laporanForm");
  const input = document.querySelector('input[name="evidence[]"]');
  const previewWrapper = document.querySelector(".image-preview-wrapper");
  const fileCount = document.querySelector(".file-count");

  // Category selection handling
  categorySelect.addEventListener("change", function () {
    if (this.value === "Lainnya") {
      otherCategoryGroup.style.display = "block";
      document
        .querySelector('[name="other_category"]')
        .setAttribute("required", "required");
    } else {
      otherCategoryGroup.style.display = "none";
      document
        .querySelector('[name="other_category"]')
        .removeAttribute("required");
      document.querySelector('[name="other_category"]').value = "";
    }
  });

  // File upload and preview handling
  input.addEventListener("change", function () {
    previewWrapper.innerHTML = "";
    fileCount.textContent = this.files.length + " files";

    if (this.files.length > 3) {
      alert("Maksimal 3 foto yang dapat diunggah");
      this.value = "";
      fileCount.textContent = "0 files";
      return;
    }

    Array.from(this.files).forEach((file, index) => {
      if (!file.type.match("image.*")) {
        alert(`File ${file.name} bukan gambar`);
        return;
      }

      if (file.size > 2 * 1024 * 1024) {
        alert(`File ${file.name} melebihi 2MB`);
        return;
      }

      const reader = new FileReader();
      const preview = document.createElement("div");
      preview.className = "preview-box";

      reader.onload = function (e) {
        preview.innerHTML = `
          <div class="preview-content">
            <img src="${e.target.result}" class="img-preview" alt="Preview ${
          index + 1
        }">
            <span class="preview-number">Foto ${index + 1}</span>
            <button type="button" class="preview-close" onclick="removeFile(${index})">&times;</button>
          </div>
        `;
      };

      reader.readAsDataURL(file);
      previewWrapper.appendChild(preview);
    });
  });
});

// File removal handler
function removeFile(index) {
  const input = document.querySelector('input[name="evidence[]"]');
  const fileCount = document.querySelector(".file-count");
  const previewWrapper = document.querySelector(".image-preview-wrapper");

  const dt = new DataTransfer();
  const files = Array.from(input.files);

  files.splice(index, 1);
  files.forEach((file) => dt.items.add(file));
  input.files = dt.files;

  previewWrapper.innerHTML = "";
  files.forEach((file, i) => {
    const preview = document.createElement("div");
    preview.className = "preview-box";

    const reader = new FileReader();
    reader.onload = function (e) {
      preview.innerHTML = `
        <div class="preview-content">
          <img src="${e.target.result}" class="img-preview" alt="Preview ${
        i + 1
      }">
          <span class="preview-number">Foto ${i + 1}</span>
          <button type="button" class="preview-close" onclick="removeFile(${i})">&times;</button>
        </div>
      `;
    };

    reader.readAsDataURL(file);
    previewWrapper.appendChild(preview);
  });

  fileCount.textContent = input.files.length + " files";
}
