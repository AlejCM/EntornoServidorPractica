<?php
class Producto {
    public int $id_producto;
    public string $nombreProducto;
    public float $precio;
    public string $descripcion;
    public int $cantidad;
    public string $imagen;

    function __construct(int $id_producto, string $nombreProducto, float $precio, string $descripcion, int $cantidad, string $imagen){
        $this -> id_producto = $id_producto;
        $this -> nombreProducto = $nombreProducto;
        $this -> precio = $precio;
        $this -> descripcion = $descripcion;
        $this -> cantidad = $cantidad;
        $this -> imagen = $imagen;
    }
}
?>