<?php
/*
 * Programming M6 Lesson 4: 6D: Document Automation
 */

$lessonData = [
    'title' => '6D: Document Automation',
    'objective' => 'The student can generate PDF documents and Word documents in Python using libraries like reportlab and python-docx.',
    'simple_explanation' => "Python libraries allow you to programmatically create documents like PDFs and Word files. This is useful for generating reports, invoices, and other formatted output.",
    'astro_explanation' => "Astro uses document automation to generate mission reports, scientific papers, and maintenance logs that need to be shared with mission control and Earth-based teams.",
    'code_example' => "# Generating a simple PDF\ntry:\n    from reportlab.lib.pagesizes import letter\n    from reportlab.pdfgen import canvas\n    \n    # Create a PDF file\n    c = canvas.Canvas(\"astro_report.pdf\", pagesize=letter)\n    width, height = letter\n    \n    # Add title\n    c.setFont(\"Helvetica-Bold\", 16)\n    c.drawString(50, height - 50, \"Astro Mission Report\")\n    \n    # Add content\n    c.setFont(\"Helvetica\", 12)\n    c.drawString(50, height - 80, \"Mission Status: Nominal\")\n    c.drawString(50, height - 100, \"Current Time: 2023-01-15 14:30:00 UTC\")\n    c.drawString(50, height - 120, \"Oxygen Level: 98.2%\")\n    \n    # Save the PDF\n    c.save()\n    print(\"PDF report generated: astro_report.pdf\")\nexcept ImportError:\n    print(\"reportlab not installed. Install with: pip install reportlab\")\n\n# Generating a simple Word document\ntry:\n    from docx import Document\n    from docx.shared import Inches\n    \n    # Create a new Word document\n    doc = Document()\n    \n    # Add title\n    doc.add_heading('Astro Mission Log', 0)\n    \n    # Add paragraph\n    p = doc.add_paragraph('Mission day 45: All systems nominal.')\n    p.add_run(' Crew health good.').bold = True\n    \n    # Add a table\n    table = doc.add_table(rows=1, cols=3)\n    hdr_cells = table.rows[0].cells\n    hdr_cells[0].text = 'Timestamp'\n    hdr_cells[1].text = 'Sensor'\n    hdr_cells[2].text = 'Value'\n    \n    # Add data rows\n    row_cells = table.add_row().cells\n    row_cells[0].text = '2023-01-15 10:00:00'\n    row_cells[1].text = 'Temperature'\n    row_cells[2].text = '22.5°C'\n    \n    # Save the document\n    doc.save('astro_log.docx')\n    print(\"Word document generated: astro_log.docx\")\nexcept ImportError:\n    print(\"python-docx not installed. Install with: pip install python-docx\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that creates a simple PDF with the text \"Hello from Astro!\" using reportlab.',
        'starter_code' => "from reportlab.lib.pagesizes import letter\nfrom reportlab.pdfgen import canvas\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain why generating documents programmatically is useful for space missions like Astro\\'s.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the purpose of the reportlab library in Python?',
            'options' => [
                'a' => 'To read and write Excel files',
                'b' => 'To generate PDF documents',
                'c' => 'To connect to databases',
                'd' => 'To make HTTP requests',
            ],
            'correct' => 'b',
            'explanation' => "reportlab is a Python library for generating PDF documents programmatically.",
        ],
        [
            'question' => 'Which Python library is commonly used for creating and modifying Word (.docx) files?',
            'options' => [
                'a' => 'reportlab',
                'b' => 'openpyxl',
                'c' => 'python-docx',
                'd' => 'pandas',
            ],
            'correct' => 'c',
            'explanation' => "python-docx is the standard library for creating, reading, and modifying Microsoft Word documents in Python.",
        ],
    ],
];

return $lessonData;
?>