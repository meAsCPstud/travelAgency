<?php
//crear la conexión
function getPDOConnection($dbname) {
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Conexión fallida - ERROR: " . $e->getMessage();
        exit();
    }
}

// tabla VUELOS
$conn = getPDOConnection('AGENCIA');
echo "Conexión exitosa con PDO a AGENCIA<br>";

$origen = $_POST['origen'];
$destino = $_POST['destino'];
$fecha = $_POST['fecha'];
$precio = $_POST['precio'];

$sql = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) VALUES (:origen, :destino, :fecha, 100, :precio)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':origen', $origen);
$stmt->bindParam(':destino', $destino);
$stmt->bindParam(':fecha', $fecha);
$stmt->bindParam(':precio', $precio);

if ($stmt->execute()) {
    echo "Vuelo agregado exitosamente.<br>";
} else {
    echo "Error al agregar el vuelo.<br>";
}

// tabla RESERVA
$conn = getPDOConnection('AGENCIA');
echo "Conexión exitosa con PDO a AGENCIA<br>";

// INSERT para varias reservas
$sql = "INSERT INTO RESERVA (id_reserva, id_cliente, fecha_reserva, id_vuelo, id_hotel) VALUES
(1, 101, '2024-08-01', 1, 1),
(2, 102, '2024-08-02', 2, 2),
(3, 103, '2024-08-03', 3, 3),
(4, 104, '2024-08-04', 4, 4),
(5, 105, '2024-08-05', 5, 5),
(6, 106, '2024-08-06', 6, 6),
(7, 107, '2024-08-07', 7, 7),
(8, 108, '2024-08-08', 8, 8),
(9, 109, '2024-08-09', 9, 9),
(10, 110, '2024-08-10', 10, 10);";

try {
    $conn->exec($sql);
    echo "Reservas registradas exitosamente.<br>";
} catch (PDOException $e) {
    echo "Error al registrar reservas: " . $e->getMessage() . "<br>";
}

//query para hoteles + de 2 reservas:
$sql = "SELECT H.nombre, COUNT(R.id_hotel) AS num_reservas
        FROM HOTEL H
        JOIN RESERVA R ON H.id_hotel = R.id_hotel
        GROUP BY H.id_hotel
        HAVING COUNT(R.id_hotel) > 2";

$result = $conn->query($sql);

if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Hotel: " . $row["nombre"]. " - Reservas: " . $row["num_reservas"]. "<br>";
    }
} else {
    echo "No se encontraron hoteles con más de 2 reservas.<br>";
}

// SELECT para la tabla VUELO
$sql = "SELECT * FROM VUELO";
$result = $conn->query($sql);

echo "<h2>Vuelos</h2>";
if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row["id_vuelo"]. " - Origen: " . $row["origen"]. " - Destino: " . $row["destino"]. " - Fecha: " . $row["fecha"]. " - Precio: " . $row["precio"]. "<br>";
    }
} else {
    echo "No se encontraron vuelos.<br>";
}

$conn = null; //cierra la conexión

