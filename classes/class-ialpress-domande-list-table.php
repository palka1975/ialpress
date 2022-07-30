<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ialpress_Domande_List_Table extends WP_List_Table {
    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'mii_domanda',     //singular name of the listed records
            'plural'    => 'mii_domande',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch ($column_name) {
            case 'indirizzo':
            case 'email':
            case 'telefono':
            case 'corso':
            case 'newsletter':
            case 'sede_corso':
                return $item[$column_name];
                break;
            case 'tipologia':
                if ( ! empty( $item[$column_name] ) ) {
                    $_cont = '<a href="' . add_query_arg( 'f_tipologia', $item[$column_name]->term_id ) . '">' . $item[$column_name]->name . '</a>';
                    return $_cont;
                }
                else return '-';
                break;
            case 'updated':
                return date('d/m/Y H:i', strtotime($item[$column_name]));
                break;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){
        
        //Build row actions
        $actions = array(
            // 'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            // 'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
            'view' => sprintf('<a href="?page=%s&action=%s&ialman_domanda=%s">View</a>',$_REQUEST['page'],'view',$item['ID']),
        );
        
        //Return the title contents
        return sprintf('<strong><a href="?page=%s&action=view&ialman_domanda=%s">%s</a></strong> %s',
            $_REQUEST['page'],
            $item['ID'],
            ucwords( strtolower( $item['title'] ) ),
            $this->row_actions( $actions )
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have its own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'         => 'Cognome e Nome',
            'indirizzo'     => 'Indirizzo',
            'email'         => 'Email',
            'telefono'      => 'Telefono',
            'corso'         => 'Corso',
            'sede_corso'    => 'Sede Corso',
            'tipologia'     => 'Tipologia Corso',
            'newsletter'    => 'Newsletter',
            'updated'       => 'Data Preiscrizione',
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'updated'   => array('updated',true),
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'archive'    => 'Archivia'
        );
        return $actions;
    }

    /** ************************************************************************
     * Gets the list of views available on this table.
     *
     * The format is an associative array:
     * - `'id' => 'link'`
     *
     **************************************************************************/
    function get_views() {
        $views = array();
        $_ialman = new Ialman_Ops();
        $class = '';

        // tutte
        $args = array(
            'page' => 'ialpress-domande',
        );
        $count = $_ialman->countCurrentDomande();
        if ( !isset( $_REQUEST['dom_status'] ) OR empty( $_REQUEST['dom_status'] ) ) $class = 'current';
        $views['all'] = $this->get_edit_link( $args, 'Recenti <span class="count">('.$count.')</span>', $class );

        // archiviate
        $args = array(
            'page' => 'ialpress-domande',
            'dom_status' => 'archived',
        );
        $count = $_ialman->countCurrentDomande( true );
        if ( ! empty( $_REQUEST['dom_status'] ) AND $_REQUEST['dom_status']=='archived' ) $class = 'current';
        else $class = '';
        $views['archived'] = $this->get_edit_link( $args, 'Archiviate <span class="count">('.$count.')</span>', $class );

        return $views;
    }

    protected function get_edit_link( $args, $label, $class = '' ) {
        $url = add_query_arg( $args, 'admin.php' );

        $class_html   = '';
        $aria_current = '';
        if ( ! empty( $class ) ) {
            $class_html = sprintf(
                ' class="%s"',
                esc_attr( $class )
            );

            if ( 'current' === $class ) {
                $aria_current = ' aria-current="page"';
            }
        }

        return sprintf(
            '<a href="%s"%s%s>%s</a>',
            esc_url( $url ),
            $class_html,
            $aria_current,
            $label
        );
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'archive'===$this->current_action() ) {

            // security check!
            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];
                if ( ! wp_verify_nonce( $nonce, $action ) ) wp_die( 'Nope! Security check failed!' );
            }
            // archive selected elements
            $selected_domande = $_REQUEST['mii_domanda'];
            $_ialman = new Ialman_Ops();
            $_ialman->archiveDomande( $selected_domande );
        }
        
    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb;
        $per_page = 25;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $args = array(
            'archived' => 0,
        );
        if ( ! empty( $_REQUEST['dom_status'] ) AND $_REQUEST['dom_status']=='archived' ) $args['archived'] = 1;

        // CAMPO RICERCA
        $s = !empty($_REQUEST['s']) ? $_REQUEST['s'] : false;
        $filtra_tipologia = !empty($_REQUEST['f_tipologia']) ? $_REQUEST['f_tipologia'] : false;
        if ( !empty($s) ) {
            $args['s'] = $s;
        } else {
            // DATE
            $date_from = !empty($_REQUEST['date_from']) ? $_REQUEST['date_from'] : false;
            $date_to = !empty($_REQUEST['date_to']) ? $_REQUEST['date_to'] : false;
            if ( $date_from ) $args['date_from'] = $date_from;
            if ( $date_to ) $args['date_to'] = $date_to;
            
            // ORDER
            $orderby = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : false;
            if ( $orderby ) {
                $order = !empty($_REQUEST['order']) ? strtoupper($_REQUEST['order']) : 'ASC';
                $args['orderby'] = $orderby;
                $args['order'] = $order;
            }
        }
        $_ialman = new Ialman_Ops();
        $results = $_ialman->getDomande( $args );
        $data = array();
        foreach ($results as $row) {
            $insert = true;
            $ind = $row->indirizzo . '<br>' . $row->cap . ' ' . $row->recapito . ' (' . $row->prov . ')<br>' . $row->stato;
            $nl = intval( get_post_meta( $row->ID, 'isc_newsletter', true ) );
            $corso = $_ialman->getImportedCommessa( $row->id_corso )[0];
            $tipologia_corsi = false;
            if ( ! empty( $corso ) OR $row->id_corso=='73594' ) {
                if ( $row->id_corso=='73594' ) {
                    // for testing purposes only
                    $tipologia_corsi = get_term( 16, 'tipologia_corsi' );
                    $sede_corso = get_term( 22, 'sede_corso' );
                } else {
                    if ( is_object( $corso ) ) {                    
                        $terms = get_the_terms( $corso->ID, 'tipologia_corsi' );
                        if ( ! empty( $terms ) ) {
                            $tipologia_corsi = $terms[0];
                        }
                    }

                    if ( is_object( $corso ) ) {
                        $terms_sedi = get_the_terms( $corso->ID, 'sede_corso' );
                        if ( ! empty( $terms_sedi ) ) {
                            $sede_corso = $terms_sedi[0];
                        }
                    } else $sede_corso = [''];
                }
            }
            if ( ! empty( $filtra_tipologia ) ) {
                if ( $tipologia_corsi->term_id != $filtra_tipologia ) $insert = false;
            }
            if ( $insert ) {
                $data[] = array(
                    'ID' => $row->ID,
                    'title' => $row->cognome . ' ' . $row->nome,
                    'indirizzo' => $ind,
                    'email' => $row->mail,
                    'telefono' => $row->telefono,
                    'corso' => $row->descrizione,
                    'newsletter' => $nl==1 ? 'Sì' : 'No',
                    'sede_corso' => is_object( $sede_corso ) ? $sede_corso->name : '',
                    'tipologia' => $tipologia_corsi,
                    'updated' => $row->update_timestamp,
                );
            }
        }
        
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  // total number of items
            'per_page'    => $per_page,                     // how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   // total number of pages
        ) );
    }

    function extra_tablenav( $which )
    {
        $date_from = '';
        $date_to = '';
        if ( !empty($_REQUEST['date_from']) ) $date_from = $_REQUEST['date_from'];
        if ( !empty($_REQUEST['date_to']) ) $date_to = $_REQUEST['date_to'];
        $filtra_tipologia = !empty($_REQUEST['f_tipologia']) ? $_REQUEST['f_tipologia'] : false;

        $tipologie = get_terms( array(
            'taxonomy' => 'tipologia_corsi',
            'hide_empty' => false,
        ) );
        $select_html = '<select name="f_tipologia" id="f_tipologia"><option value="">Scegli..</option>';
        foreach ($tipologie as $tipo) {
            $select_html .= '<option value="' . $tipo->term_id . '"';
            if ( ! empty( $filtra_tipologia ) AND $filtra_tipologia==$tipo->term_id ) $select_html .= ' selected="selected"';
            $select_html .= '>' . $tipo->name . '</option>';
        }
        $select_html .= '</select>';
        switch ( $which )
        {
            case 'top':
                echo '<div class="alignleft actions">
                    <label class="" for="dateFrom">Da: </label><input type="date" id="dateFrom" name="date_from" value="' . $date_from . '">
                    <label class="" for="dateTo">A: </label><input type="date" id="dateTo" name="date_to" value="' . $date_to . '">
                    <label class="" for="f_tipologia">Tipologia Corso: </label> ' . $select_html . '
                    <input type="hidden" name="lang" value="it">

                    <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filtra">
                </div>';
                break;

            case 'bottom':
                // Your html code to output
                break;
        }
    }

}