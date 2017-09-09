<?php
include_once("IModel.php");
include_once("Book.php");

/** The Model is the class holding data about a collection of books.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
class DBModel implements IModel
{
    /**
      * The PDO object for interfacing the database
      *
      */
    protected $db = null;

    /**
	 * @throws PDOException
     */
    public function __construct($db = null)
    {
	    if ($db)
		{
			$this->db = $db;
		}
		else
		{
            // Create PDO connection
            $this->db = new PDO('mysql:host=localhost;dbname=Database_Oblig1;charset=utf8mb4;port=8889;', 'root', 'root');
		}
    }

    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
	 * @throws PDOException
     */
    public function getBookList()
    {
        // Creating a booklist to be displayed.
        $booklist = array();
        // Creating an array from the table of the database.
        $stmt = $this->db->prepare("SELECT * FROM book");
        $stmt->execute();
        // For each row in the array:
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
            // Adding to the list.
			$booklist[] = new Book($row['Title'], $row['Author'], $row['Description'], $row['id']);
		}
        return $booklist;
    }

    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
	 * @throws PDOException
     */
    public function getBookById($id)
    {
        // Id not numeric.
        if(!is_numeric($id))
        {
            echo "ID not numeric.";
            return NULL;
        }
        // Creating an array from the table of the database.
		$stmt = $this->db->prepare("SELECT * FROM book WHERE id = :id");
        $stmt->execute(array(":id"=>$id));
        // Putting the first row into $row-variable.
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // If the $row is not empty.
        if($row)
        {
            // Creating a book with the row-information.
            $book = new Book($row['Title'], $row['Author'], $row['Description'], $row['id']);
            return $book;
        }
        // The $row is empty - no such book id.
        else
        {
            echo "Could not find book.";
            return NULL;
        }
    }

    /** Adds a new book to the collection.
     * @param $book Book The book to be added - the id of the book will be set after successful insertion.
	 * @throws PDOException
     */
    public function addBook($book)
    {
        // Mandatory fields are not filled.
        if(empty($book->title) || empty($book->author))
        {
            echo "Title and Author must be set.";
            return NULL;
        }
        // Adding new book.
        $stmt = $this->db->prepare(  "INSERT INTO Book(title, author, description)
                            VALUES(:title, :author, :description)");
        $stmt->execute(array(':title' => $book->title, ':author' => $book->author, ':description' => $book->description));
    }

    /** Modifies data related to a book in the collection.
     * @param $book Book The book data to be kept.
     * @todo Implement function using PDO and a real database.
     */
    public function modifyBook($book)
    {
        // Mandatory fields are not filled.
        if(empty($book->title) || empty($book->author))
        {
            echo "Title and Author must be set.";
            return NULL;
        }
        // Updating the book.
        $stmt = $this->db->prepare("UPDATE book SET title=:title, author=:author, description=:description WHERE id=:id");
        $stmt->execute(array(':title' => $book->title, ':author' => $book->author, ':description' => $book->description, ':id'=>$book->id));
    }

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     */
    public function deleteBook($id)
    {
        // Id is not numeric.
        if(!is_numeric($id))
        {
            echo "ID not numeric.";
            return NULL;
        }
        // Creating an array from the table of the database.
		$stmt = $this->db->prepare("SELECT * FROM book WHERE id = :id");
        $stmt->execute(array(":id"=>$id));
        // Putting the first row into $row-variable.
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // If the $row is not empty.
        if($row)
        {
            // Finds and deletes the book from database.
            $stmt = $this->db->prepare("DELETE FROM book WHERE id=:id");
            $stmt->execute(array(':id'=>$id));
            echo "Deleted";
        }
        // No book with that ID.
        else
        {
            echo "Could not find that book.";
        }
    }
}

?>
