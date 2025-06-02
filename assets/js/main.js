document.addEventListener("DOMContentLoaded", () => {
  // Declare QRScanner and Tesseract variables
  const QRScanner = window.QRScanner
  const Tesseract = window.Tesseract
  const bootstrap = window.bootstrap

  // Inicializar el escáner QR
  const qrScanner = new QRScanner({
    targetElement: "#scanner-view",
    messageElement: "#scanner-message",
  })

  // Control de pestañas del formulario
  document.getElementById("btnManual").addEventListener("click", function () {
    showForm("formManual")
    setActiveButton(this)
    qrScanner.stop()
  })

  document.getElementById("btnQR").addEventListener("click", function () {
    showForm("formQR")
    setActiveButton(this)
    qrScanner.start().catch(handleCameraError)
  })

  document.getElementById("btnOCR").addEventListener("click", function () {
    showForm("formOCR")
    setActiveButton(this)
    qrScanner.stop()
  })

  // Botones del escáner
  document.getElementById("stopScanner").addEventListener("click", () => {
    qrScanner.stop()
    document.getElementById("btnManual").click()
  })

  document.getElementById("switchCamera").addEventListener("click", () => {
    qrScanner.switchCamera()
  })

  document.getElementById("retryCamera").addEventListener("click", () => {
    document.getElementById("cameraError").classList.add("hidden")
    qrScanner.start().catch(handleCameraError)
  })

  document.getElementById("scanAgain").addEventListener("click", () => {
    document.getElementById("qrResult").classList.add("hidden")
    qrScanner.start().catch(handleCameraError)
  })

  document.getElementById("confirmQrData").addEventListener("click", () => {
    document.getElementById("registroForm").submit()
  })

  // Manejar errores de cámara
  function handleCameraError(error) {
    console.error("Error de cámara:", error)
    document.getElementById("cameraError").classList.remove("hidden")
  }

  // Escuchar eventos de detección de QR
  document.addEventListener("qrCodeDetected", (event) => {
    const { parsedData } = event.detail

    try {
      // Llenar los campos ocultos con los datos del QR
      document.getElementById("qr_codigo").value = parsedData.codigo || ""
      document.getElementById("qr_nombre").value = parsedData.nombre || ""
      document.getElementById("qr_apellido").value = parsedData.apellido || ""
      document.getElementById("qr_genero").value = parsedData.genero || ""
      document.getElementById("qr_carrera").value = parsedData.carrera_id || ""

      // Mostrar los datos detectados
      const resultHTML = `
                <p><strong>Cédula:</strong> ${parsedData.codigo || "No detectado"}</p>
                <p><strong>Nombre:</strong> ${parsedData.nombre || "No detectado"} ${parsedData.apellido || ""}</p>
                <p><strong>Carrera ID:</strong> ${parsedData.carrera_id || "No detectado"}</p>
            `

      document.getElementById("qrResultData").innerHTML = resultHTML
      document.getElementById("qrResult").classList.remove("hidden")
    } catch (e) {
      console.error("Error al procesar datos del QR:", e)
      alert("Error al procesar los datos del código QR. Formato incorrecto.")
      qrScanner.start().catch(handleCameraError)
    }
  })

  // Procesamiento OCR
  document.getElementById("processOCR").addEventListener("click", () => {
    const fileInput = document.getElementById("ocrImage")
    if (!fileInput.files || fileInput.files.length === 0) {
      alert("Por favor seleccione una imagen primero")
      return
    }

    const file = fileInput.files[0]
    const reader = new FileReader()

    reader.onload = (e) => {
      Tesseract.recognize(e.target.result, "spa", {
        logger: (m) => console.log(m),
      })
        .then(({ data: { text } }) => {
          processOCRText(text)
        })
        .catch((err) => {
          console.error(err)
          alert("Error al procesar la imagen con OCR")
        })
    }

    reader.readAsDataURL(file)
  })

  function processOCRText(text) {
    const cedulaRegex = /Cédula:\s*([0-9-]+)/i
    const nombreRegex = /Nombre:\s*([^\n]+)/i
    const apellidoRegex = /Apellido:\s*([^\n]+)/i
    const generoRegex = /Género:\s*(Hombre|Mujer|Otro)/i
    const carreraRegex = /Escuela:\s*([^\n]+)/i

    const cedulaMatch = text.match(cedulaRegex)
    const nombreMatch = text.match(nombreRegex)
    const apellidoMatch = text.match(apellidoRegex)
    const generoMatch = text.match(generoRegex)
    const carreraMatch = text.match(carreraRegex)

    if (cedulaMatch && nombreMatch && apellidoMatch) {
      document.getElementById("ocr_codigo").value = cedulaMatch[1].trim()
      document.getElementById("ocr_nombre").value = nombreMatch[1].trim()
      document.getElementById("ocr_apellido").value = apellidoMatch[1].trim()

      if (generoMatch) {
        document.getElementById("ocr_genero").value = generoMatch[1].trim()
      }

      if (carreraMatch) {
        const carreraNombre = carreraMatch[1].trim().toLowerCase()
        let carreraId = 1

        if (carreraNombre.includes("econom")) carreraId = 2
        if (carreraNombre.includes("admin")) carreraId = 3

        document.getElementById("ocr_carrera").value = carreraId
      }

      alert(`Datos detectados:\nCédula: ${cedulaMatch[1]}\nNombre: ${nombreMatch[1]} ${apellidoMatch[1]}`)
      document.getElementById("registroForm").submit()
    } else {
      alert("No se pudieron detectar todos los datos necesarios en la imagen. Por favor ingréselos manualmente.")
      console.log("Texto OCR:", text)
    }
  }

  // Funciones auxiliares
  function showForm(activeFormId) {
    const forms = ["formManual", "formQR", "formOCR"]
    forms.forEach((formId) => {
      const form = document.getElementById(formId)
      if (formId === activeFormId) {
        form.classList.remove("hidden")
      } else {
        form.classList.add("hidden")
      }
    })
  }

  function setActiveButton(activeButton) {
    const buttons = document.querySelectorAll(".btn-group .btn")
    buttons.forEach((btn) => {
      btn.classList.remove("active", "btn-primary")
      btn.classList.add("btn-secondary")
    })
    activeButton.classList.add("active", "btn-primary")
    activeButton.classList.remove("btn-secondary")
  }

  // Registro de asistencia (solo si el estudiante está registrado)
  const estudiante_registrado = true // Assuming $estudiante_registrado is a boolean value
  if (estudiante_registrado) {
    function setupAttendanceButtons(entradaId, salidaId) {
      document.getElementById(entradaId).addEventListener("click", function () {
        this.classList.add("active")
        document.getElementById(salidaId).classList.remove("active")
      })

      document.getElementById(salidaId).addEventListener("click", function () {
        this.classList.add("active")
        document.getElementById(entradaId).classList.remove("active")
      })
    }

    setupAttendanceButtons("entradaBiblioteca", "salidaBiblioteca")
    setupAttendanceButtons("entradaComputadoras", "salidaComputadoras")

    document.getElementById("confirmarBiblioteca").addEventListener("click", () => {
      const form = document.getElementById("asistenciaBibliotecaForm")
      const formData = new FormData(form)

      if (document.getElementById("entradaBiblioteca").classList.contains("active")) {
        formData.append("accion", "entrada")
      } else if (document.getElementById("salidaBiblioteca").classList.contains("active")) {
        formData.append("accion", "salida")
      } else {
        alert("Por favor seleccione Entrada o Salida")
        return
      }

      fetch("registrar_asistencia.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message)
            bootstrap.Modal.getInstance(document.getElementById("bibliotecaModal")).hide()
          } else {
            alert("Error: " + data.message)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error al registrar asistencia")
        })
    })

    document.getElementById("confirmarComputadoras").addEventListener("click", () => {
      const form = document.getElementById("asistenciaComputadorasForm")
      const formData = new FormData(form)

      const equipo = document.getElementById("equipoComputadoras").value
      if (!equipo) {
        alert("Por favor ingrese el número de equipo")
        return
      }

      if (document.getElementById("entradaComputadoras").classList.contains("active")) {
        formData.append("accion", "entrada")
      } else if (document.getElementById("salidaComputadoras").classList.contains("active")) {
        formData.append("accion", "salida")
      } else {
        alert("Por favor seleccione Inicio o Fin de uso")
        return
      }

      fetch("registrar_asistencia.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message)
            bootstrap.Modal.getInstance(document.getElementById("computadorasModal")).hide()
          } else {
            alert("Error: " + data.message)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error al registrar asistencia")
        })
    })
  }
})
