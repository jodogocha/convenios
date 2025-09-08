<?php
// app/Exports/InformesExport.php

namespace App\Exports;

use App\Models\Informe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class InformesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $informes;

    public function __construct($informes)
    {
        $this->informes = $informes;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->informes;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Convenio',
            'Institución Co-celebrante',
            'Unidad Académica',
            'Carrera',
            'Fecha Celebración',
            'Periodo Evaluado',
            'Dependencia Responsable',
            'Coordinadores',
            'Tipo Convenio',
            'Convenio Ejecutado',
            'Nº Actividades',
            'Logros Obtenidos',
            'Beneficios Alcanzados',
            'Dificultades',
            'Estado',
            'Fecha Presentación',
            'Usuario Creador',
            'Enlace Evidencias',
            'Observaciones'
        ];
    }

    /**
     * @param mixed $informe
     * @return array
     */
    public function map($informe): array
    {
        return [
            $informe->id,
            $informe->convenio ? $informe->convenio->numero_convenio : 'N/A',
            $informe->institucion_co_celebrante,
            $informe->unidad_academica,
            $informe->carrera,
            $informe->fecha_celebracion ? $informe->fecha_celebracion->format('d/m/Y') : '',
            $informe->periodo_completo,
            $informe->dependencia_responsable,
            $informe->coordinadores_texto,
            $informe->tipo_convenio,
            $informe->convenio_ejecutado ? 'Sí' : 'No',
            $informe->numero_actividades_realizadas ?? 'N/A',
            $this->limitarTexto($informe->logros_obtenidos, 100),
            $this->limitarTexto($informe->beneficios_alcanzados, 100),
            $this->limitarTexto($informe->dificultades_incidentes, 100),
            $informe->estado_texto,
            $informe->fecha_presentacion ? $informe->fecha_presentacion->format('d/m/Y') : '',
            $informe->usuarioCreador ? $informe->usuarioCreador->nombre_completo : 'N/A',
            $informe->enlace_google_drive,
            $this->limitarTexto($informe->observaciones, 200)
        ];
    }

    /**
     * Aplicar estilos a las celdas
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Estilo para todas las celdas
            'A:T' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Definir ancho de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Convenio
            'C' => 25,  // Institución
            'D' => 20,  // Unidad Académica
            'E' => 20,  // Carrera
            'F' => 12,  // Fecha Celebración
            'G' => 20,  // Periodo
            'H' => 20,  // Dependencia
            'I' => 25,  // Coordinadores
            'J' => 12,  // Tipo
            'K' => 10,  // Ejecutado
            'L' => 10,  // Nº Actividades
            'M' => 30,  // Logros
            'N' => 30,  // Beneficios
            'O' => 30,  // Dificultades
            'P' => 12,  // Estado
            'Q' => 12,  // Fecha Presentación
            'R' => 20,  // Usuario
            'S' => 40,  // Enlace
            'T' => 30,  // Observaciones
        ];
    }

    /**
     * Limitar la longitud del texto para Excel
     */
    private function limitarTexto($texto, $limite = 100)
    {
        if (!$texto) {
            return '';
        }

        if (strlen($texto) > $limite) {
            return substr($texto, 0, $limite) . '...';
        }

        return $texto;
    }
}