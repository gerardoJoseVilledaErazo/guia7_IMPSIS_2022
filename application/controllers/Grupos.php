<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grupos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        if (!isset($this->session->userdata['logged_in'])) {
            redirect("/");
        }
    }

    // FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////
    public function index()
    {
        $this->load->model('Grupo_model');
        $data = array(
            "records" => $this->Grupo_model->getAll(),
            "title" => "Grupos",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("grupos/index", $data);
        $this->load->view("shared/footer", $data);
    }

    public function insertar()
    {
        $this->load->model('Materia_model');
        $this->load->model('Profesor_model');
        $data = array(
            "materias" => $this->Materia_model->getAll(),
            "profesores" => $this->Profesor_model->getAll(),
            "title" => "Insertar grupo",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("grupos/add_edit", $data);
        $this->load->view("shared/footer");
    }

    public function modificar($id)
    {
        $this->load->model('Materia_model');
        $this->load->model('Profesor_model');
        $this->load->model('Grupo_model');
        $grupo = $this->Grupo_model->getById($id);
        $data = array(
            "materias" => $this->Materia_model->getAll(),
            "profesores" => $this->Profesor_model->getAll(),
            "grupo" => $grupo,
            "title" => "Modificar grupo",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("grupos/add_edit", $data);
        $this->load->view("shared/footer");
    }
    // FIN - FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////

    // FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////
    public function add()
    {

        // Reglas de validaci??n del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como m??ximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato v??lido.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            "idgrupo", 
            "Id Grupo", 
            "required|min_length[10]|max_length[10]|is_unique[grupos.idgrupo]"
        );
        $this->form_validation->set_rules(
            "num_grupo", 
            "Numero de Grupo", 
            "required|min_length[3]|max_length[3]|alpha_numeric_spaces"
        );
        $this->form_validation->set_rules(
            "anio", 
            "A??o", 
            "required|integer|max_length[4]"
        );
        $this->form_validation->set_rules(
            "ciclo", 
            "Ciclo", 
            "required|min_length[2]|max_length[2]"
        );
        $this->form_validation->set_rules(
            "idmateria", 
            "Materia", 
            "required|min_length[10]|max_length[10]"
        );
        $this->form_validation->set_rules(
            "idprofesor", 
            "Profesor", 
            "required|max_length[100]"
        );

        // Modificando el mensaje de validaci??n para los errores
        $this->form_validation->set_message(
            'required', 
            'El campo %s es requerido.'
        );
        $this->form_validation->set_message(
            'min_length', 
            'El campo %s debe tener al menos %s caracteres.'
        );
        $this->form_validation->set_message(
            'max_length', 
            'El campo %s debe tener como m??ximo %s caracteres.'
        );
        $this->form_validation->set_message(
            'valid_email', 
            'El campo %s no es un correo v??lido.'
        );
        $this->form_validation->set_message(
            'is_unique', 
            'El campo %s ya existe.'
        );
        $this->form_validation->set_message(
            'alpha', 
            'El campo %s debe contener solo caracteres alfabeticos.'
        );

        // Par??metros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validaci??n de los campos
        if ($this->form_validation->run()) {
            // Si la validaci??n es correcta entra ac??
            try {
                $this->load->model('Grupo_model');
                $data = array(
                    "idgrupo" => $this->input->post("idgrupo"),
                    "num_grupo" => $this->input->post("num_grupo"),
                    "anio" => $this->input->post("anio"),
                    "ciclo" => $this->input->post("ciclo"),
                    "idmateria" => $this->input->post("idmateria"),
                    "idprofesor" => $this->input->post("idprofesor"),
                );
                $rows = $this->Grupo_model->insert($data);
                if ($rows > 0) {
                    $msg = "Informaci??n guardada correctamente.";
                } else {
                    $statusCode = 500;
                    $msg = "No se pudo guardar la informaci??n.";
                }
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurri?? un error." . $ex->getMessage();
            }
        } else {
            // Si la validaci??n da error, entonces se ejecuta ac??
            $statusCode = 400;
            $msg = "Ocurrieron errores de validaci??n.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevar?? la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el c??digo de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se env??a la respuesta en formato JSON
        echo json_encode($this->data);

    }

    public function update()
    {

        // Reglas de validaci??n del formulario
        $this->form_validation->set_error_delimiters('', '');
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como m??ximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato v??lido.
         */
        $this->form_validation->set_rules(
            "idgrupo", 
            "Id Grupo", 
            "required|min_length[10]|max_length[10]"
        );
        $this->form_validation->set_rules(
            "num_grupo", 
            "Numero de Grupo", 
            "required|min_length[3]|max_length[3]|alpha_numeric_spaces"
        );
        $this->form_validation->set_rules(
            "anio", 
            "A??o", 
            "required|integer|max_length[4]"
        );
        $this->form_validation->set_rules(
            "ciclo", 
            "Ciclo", 
            "required|min_length[2]|max_length[2]"
        );
        $this->form_validation->set_rules(
            "idmateria", 
            "Materia", 
            "required|min_length[10]|max_length[10]"
        );
        $this->form_validation->set_rules(
            "idprofesor", 
            "Profesor", 
            "required|max_length[100]"
        );

        // Modificando el mensaje de validaci??n para los errores, en este caso para
        // la regla required, min_length, max_length
        $this->form_validation->set_message('required', 
        'El campo %s es requerido.');
        $this->form_validation->set_message('min_length', 
        'El campo %s debe tener al menos %s caracteres.');
        $this->form_validation->set_message('max_length', 
        'El campo %s debe tener como m??ximo %s caracteres.');
        $this->form_validation->set_message('is_unique', 
        'El campo %s ya existe.');
        $this->form_validation->set_message('alpha', 
        'El campo %s debe contener solo caracteres alfabeticos.');

        // Par??metros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validaci??n de los campos
        if ($this->form_validation->run()) {
            // Si la validaci??n es correcta entra
            try {
                $this->load->model('Grupo_model');
                $data = array(
                    "idgrupo" => $this->input->post("idgrupo"),
                    "num_grupo" => $this->input->post("num_grupo"),
                    "anio" => $this->input->post("anio"),
                    "ciclo" => $this->input->post("ciclo"),
                    "idmateria" => $this->input->post("idmateria"),
                    "idprofesor" => $this->input->post("idprofesor"),
                );
                $rows = $this->Grupo_model->update($data, $this->input->post("PK_grupo"));
                $msg = "Informaci??n guardada correctamente.";
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurri?? un error." . $ex->getMessage();
            }
        } else {
            // Si la validaci??n da error, entonces se ejecuta ac??
            $statusCode = 400;
            $msg = "Ocurrieron errores de validaci??n.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevar?? la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el c??digo de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se env??a la respuesta en formato JSON
        echo json_encode($this->data);
    }

    public function eliminar($id)
    {
        $this->load->model('Grupo_model');
        $result = $this->Grupo_model->delete($id);
        if ($result) {
            $this->session->set_flashdata('success', "Registro borrado correctamente.");
        } else {
            $this->session->set_flashdata('error', "No se pudo borrar el registro.");
        }
        redirect("grupos");
    }
    // FIN - FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////

    /* ----------------------------------------------------------------------------- */
    /*                          FUNCION PARA GENERAR REPORTE                         */
    /* ----------------------------------------------------------------------------- */

    public function report_todos_los_grupos(){//
        //Se carga la libreria para generar tablas
        $this->load->library("table");
        //Se carga la libreria Report que acabamos de crear
        $this->load->library("Report");
        
        $pdf = new Report(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTFF-8', false);
        $pdf->titulo = "Listado de Grupos";//
        //Informacion del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Gerardo Villeda');
        $pdf->SetTitle('Listado de Grupos');//
        $pdf->SetSubject('Report generado usando Codeigniter y TCPDF');
        $pdf->SetKeywords('TCPDF, PDF, MySQL, Codeigniter');

        //Informacion por defecto del encabezado

        //Fuente de encabezado y pie de pagina
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        //Fuente por defecto Monospaced
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Margenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->setHeaderMargin(15);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        //Quiebre de pagina automatico
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        //Factor de escala de imagen
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //Fuente del contenido
        $pdf->SetFont('Helvetica','',10);

        // --------------------------------------------------------------------

        //Generar la tabla y su informacion
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1">',
            'heading_cell_start' => '<th style="font-weight: bold; color:white;
            background-color: #13CDF7">',
        );

        $this->table->set_template($template);

        $this->table->set_heading('Id Grupo','Numero de Grupo', 'A??o', 'Ciclo', 'Materia', 'Profesor');//

        $this->load->model('Grupo_model');
        $grupos = $this->Grupo_model->getAll();//aqui

        foreach ($grupos as $grupo):
            $this->table->add_row($grupo->idgrupo, $grupo->num_grupo, $grupo->anio, $grupo->ciclo, $grupo->materia, $grupo->nombreCompleto);
        endforeach;

        $html = $this->table->generate();
        //Generar la informacion de la tabla

        //A??adir pagina
        $pdf->AddPage();

        //Contenido de salida en HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        //Reiniciar puntero a la ultima pagina
        $pdf->lastPage();

        //cerrar y mostrar el reporte
        $pdf->Output(md5(time()) . '.pdf', 'I');    
    }
    /* ----------------------------------------------------------------------------- */
    /*                         /FUNCION PARA GENERAR REPORTE                         */
    /* ----------------------------------------------------------------------------- */

}
