
## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/pmicalculator.git
    cd pmicalculator
    ```

2. Install the required Python packages:
    ```bash
    pip install -r requirements.txt
    ```

3. Make sure you have a web server (like Apache or Nginx) with PHP support installed.

## Usage

1. Open the `index.html` file in your web browser or deploy it on a local server.

2. Fill out the form with the following inputs:
    - **Lichaamstemperatuur** (Body Temperature)
    - **Omgevingstemperatuur** (Ambient Temperature)
    - **Lichaamsgewicht** (Body Weight)
    - **Lichaamsbedekking** (Body Covering)
    - **Omgevingsfactoren** (Environmental Factors)
    - **Date**
    - **Time**

3. Click the `Submit` button to calculate the PMI.

4. The result will be displayed in a modal.

## Files

- **calc.py**: The Python script that calculates the PMI based on the provided inputs.
- **pmi.php**: Handles the form submission and calls the Python script.
- **pmistyles.css**: Contains the CSS for styling the HTML page.
- **test_calc.py**: Contains the test cases for `calc.py`.

## PHP Script

The PHP script (`pmi.php`) handles the form submission and executes the `calc.py` Python script with the provided input values. The results are displayed in a modal on the HTML page.

## JavaScript

The JavaScript in the HTML file manages the modal display:
- **closeModal**: Closes the modal when the user clicks on the close button.
- **window.onclick**: Closes the modal when the user clicks anywhere outside the modal.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Feel free to open issues or submit pull requests for improvements or bug fixes.

## Authors

- Your Name - [Your GitHub](https://github.com/yourusername)
