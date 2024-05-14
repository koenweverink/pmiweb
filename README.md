
## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/koenweverink/pmiweb.git
    cd pmicalculator
    ```

2. Install the required Python packages:
    ```bash
    pip install -r requirements.txt
    ```

3. Make sure you have a web server (like Apache or Nginx) with PHP support installed.

## Usage

1. Open the `pmi.php` file in your web browser or deploy it on a local server.

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

## Project Structure

project_root/
│
├── pycache/ # Compiled Python files
│
├── src/ # Source files
│ ├── calc.py # Python source code for PMI calculation
│ └── pmi.php # PHP script handling form submission and calling the Python script
│
├── tests/ # Test files
│ └── test_calc.py # Python test code for calc.py
│
├── styles/ # CSS and other styling files
│ └── pmistyles.css # CSS for the project
│
├── index.html # HTML file containing the form and modal for displaying results
│
├── LICENSE # License
│
└── README.md # Project documentation

## PHP Script

The PHP script (`pmi.php`) handles the form submission and executes the `calc.py` Python script with the provided input values. The results are displayed in a modal on the HTML page.

## calc.py

The `calc.py` script is responsible for calculating the PMI based on the provided parameters. It uses a set of predefined factors and weight thresholds to determine the PMI in hours. The script takes the following command-line arguments:

```bash
python calc.py <cover> <surfact> <t_rectum_c> <t_ambient_c> <body_wt_kg> <date> <time>
```

### Functions
- calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg): Calculates the PMI based on the given conditions and returns the PMI in hours.
- get_right_side(t_ambient_c, bigB, f): Helper function to compute the right side of the PMI equation based on temperature and a factor.
- get_corrective_factor(cover, surFact): Retrieves the corrective factor based on clothing and environment.
- get_uncertainty(t_ambient_c, body_wt_kg, best_time, cover, surFact): Calculates the uncertainty based on the ambient temperature, body weight, and other factors.
- get_weight_category(wt): Retrieves the weight category based on the body weight.
- get_times(pmi, uncertainty, date, time): Calculates the time intervals based on PMI and uncertainty, returning the estimated time of the event.

## JavaScript

The JavaScript in the HTML file manages the modal display:
- **closeModal**: Closes the modal when the user clicks on the close button.
- **window.onclick**: Closes the modal when the user clicks anywhere outside the modal.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Feel free to open issues or submit pull requests for improvements or bug fixes.

## Authors

- Koen Weverink - [Your GitHub](https://github.com/koenweverink)
