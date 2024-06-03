import unittest, sys, os

current_dir = os.path.dirname(__file__)  # Gets the directory of the current file
parent_dir = os.path.dirname(current_dir)  # Moves up to the parent directory
src_path = os.path.join(parent_dir, 'src')  # Specifies the src directory path
sys.path.append(src_path)


from calc import PMICalculator

class TestPMICalculator(unittest.TestCase):
    def setUp(self):
        self.calc = PMICalculator()

    def test_calc_pmi(self):
        # Test case 1: Valid input values
        cover = "Een of twee dunne lagen"
        surfact = "Droog lichaam binnen"
        t_rectum_c = 33.5
        t_ambient_c = 25.0
        body_wt_kg = 70.0
        expected_pmi = 7

        result = self.calc.calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg)
        self.assertAlmostEqual(result, expected_pmi, places=2)

    def test_get_uncertainty(self):
        # Test case 1: Valid input values
        t_ambient_c = 25.0
        body_wt_kg = 70.0
        pmi = 4.5
        cover = "Een of twee dunne lagen"
        surfact = "Droog lichaam binnen"
        expected_uncertainty = 2.8

        result = self.calc.get_uncertainty(t_ambient_c, body_wt_kg, pmi, cover, surfact)
        self.assertAlmostEqual(result, expected_uncertainty, places=2)

    def test_low_body_weight(self):
        # Test case 3: Low Body Weight
        cover = "Een of twee dunne lagen"
        surfact = "Droog lichaam binnen"
        t_rectum_c = 33.5
        t_ambient_c = 25.0
        body_wt_kg = 10.0

        result = self.calc.calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg)
        self.assertEqual(result, "Error: Er is een hoge mate van onzekerheid door het lage lichaamsgewicht.")

    def test_surtemp_higher_than_rectemp(self):
        # Test case 4: Ambient temperature higher than rectal temperature
        cover = "Een of twee dunne lagen"
        surfact = "Droog lichaam binnen"
        t_rectum_c = 25.0
        t_ambient_c = 33.5
        body_wt_kg = 70.0

        result = self.calc.calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg)
        self.assertEqual(result, "Error: De lichaamstemperatuur is lager dan de omgevingstemperatuur")

    def test_high_uncertainty(self):
        # Test case 5: High Uncertainty
        cover = "Een of twee dunne lagen"
        surfact = "Droog lichaam binnen"
        t_rectum_c = 33.5
        t_ambient_c = 25.0
        body_wt_kg = 400.0

        result = self.calc.calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg)
        self.assertEqual(result, "Error: Er is een hoge mate van onzekerheid.")

if __name__ == '__main__':
    unittest.main()