import math, sys, datetime

class PMICalculator:
    def __init__(self):
        """
        Initializes the PMICalculator with predefined factors and weight thresholds.
        """
        self.factors = {
            'Droog lichaam, binnen': {
                'Naakt': 1.0,
                'Een of twee dunne lagen': 1.1,
                'Een of twee dikke lagen': 1.2,
                'Twee of drie lagen': 1.2,
                'Drie of vier lagen': 1.3,
                'Meer lagen': 1.4,
                'Licht beddengoed': 1.8,
                'Zwaar beddengoed': 2.4,
            },
            'Droog lichaam, buiten': {
                'Naakt': 0.75,
                'Een of twee dunne lagen': 0.9,
                'Een of twee dikke lagen': 0.9,
                'Twee of drie lagen': 1.2,
                'Drie of vier lagen': 1.3,
                'Meer lagen': 1.4,
                'Licht beddengoed': 1.8,
                'Zwaar beddengoed': 2.4,
            },
            'Nat lichaam, binnen': {
                'Naakt': 0.5,
                'Een of twee dunne lagen': 0.8,
                'Een of twee dikke lagen': 1.1,
                'Twee of drie lagen': 1.2,
                'Drie of vier lagen': 1.2,
                'Meer lagen': 1.2,
                'Licht beddengoed': 1.2,
                'Zwaar beddengoed': 1.2,
            },
            'Nat lichaam, buiten': {
                'Naakt': 0.7,
                'Een of twee dunne lagen': 0.7,
                'Een of twee dikke lagen': 0.9,
                'Twee of drie lagen': 0.9,
                'Drie of vier lagen': 0.9,
                'Meer lagen': 0.9,
                'Licht beddengoed': 0.9,
                'Zwaar beddengoed': 0.9,
            },
            'Stilstaand water': {
                'Naakt': 0.5,
                'Een of twee dunne lagen': 0.7,
                'Een of twee dikke lagen': 0.8,
                'Twee of drie lagen': 0.9,
                'Drie of vier lagen': 1.0,
                'Meer lagen': 1.0,
                'Licht beddengoed': 1.0,
                'Zwaar beddengoed': 1.0,
            },
            'Stromend water': {
                'Naakt': 0.35,
                'Een of twee dunne lagen': 0.5,
                'Een of twee dikke lagen': 0.7,
                'Twee of drie lagen': 0.8,
                'Drie of vier lagen': 0.9,
                'Meer lagen': 1.0,
                'Licht beddengoed': 1.0,
                'Zwaar beddengoed': 1.0,
            },
        }

        self.weight_thresholds = {
            10: (3, 5, 7),
            15: (4, 4.2, 9),
            20: (5, 5.5, 11),
            30: (6, 7.2, 15),
            40: (8, 9, 18),
            50: (9, 11, 22),
            60: (11, 13, 26),
            70: (13, 15, 30),
            80: (14, 17, 34),
            90: (16, 19, 37),
            100: (18, 21, 42),
            110: (20, 23, 46),
            120: (20, 25, 50),
            140: (20, 30, 60),
            160: (20, 36, 72),
            180: (20, 42, 82),
            'default': (20, 50, 90)
        }
        
    def calc_pmi(self, cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg):
        """
        Calculates the PMI based on given conditions and returns the PMI in hours.
        """
        if t_ambient_c > t_rectum_c:
            return "Error: De lichaamstemperatuur is lager dan de omgevingstemperatuur"
        
        if body_wt_kg < 11:
            return "Error: Er is een hoge mate van onzekerheid door het lage lichaamsgewicht."

        corrective_factor = self.get_corrective_factor(cover, surfact)
        left_side = (t_rectum_c - t_ambient_c) / (37.2 - t_ambient_c)
        bigB = (-1.2815 * (corrective_factor * body_wt_kg) ** -0.625 + 0.0284)

        best_time = 0.0
        for proposed_time in (i * 0.1 for i in range(1000)):  # up to 100 minutes, in 0.1 minute increments
            if abs(left_side - self.get_right_side(t_ambient_c, bigB, proposed_time)) < abs(left_side - self.get_right_side(t_ambient_c, bigB, best_time)):
                best_time = proposed_time

        uncertainty = self.get_uncertainty(t_ambient_c, body_wt_kg, best_time, cover, surfact)
        if uncertainty == 69:
            return "Error: Er is een hoge mate van onzekerheid."

        return int(round(best_time, 2))
    

    def get_right_side(self, t_ambient_c, bigB, f):
        """
        Helper function to compute the right side of the PMI equation based on temperature and a factor.
        """
        if t_ambient_c <= 23:
            return 1.25 * math.exp(bigB * f) - 0.25 * math.exp(5 * bigB * f)
        else:
            return 1.11 * math.exp(bigB * f) - 0.11 * math.exp(10 * bigB * f)


    def get_corrective_factor(self, cover, surFact):
        """
        Retrieves the corrective factor based on clothing and environment.
        """
        return self.factors.get(surFact, {}).get(cover, 1.0)
    

    def get_uncertainty(self, t_ambient_c, body_wt_kg, best_time, cover, surFact):
        category1, category2, category3 = self.get_weight_category(body_wt_kg)
        if t_ambient_c > 23 and best_time >= category3:
            return 69
        if best_time >= category3:
            return 69
        if best_time >= category2:
            return self.Category4570(cover, surFact)
        if best_time >= category1:
            return self.Category3245(cover, surFact)
        return 2.8

    def Category4570(self, cover, surFact):
        return 7 if cover != 'Naked' and surFact == 'StillAirBodyDry' else 4.5

    def Category3245(self, cover, surFact):
        return 4.5 if cover != 'Naked' and surFact == 'StillAirBodyDry' else 3.2

    def get_weight_category(self, wt):
        for weight, times in sorted((k, v) for k, v in self.weight_thresholds.items() if isinstance(k, int)):
            if wt <= weight:
                return times
        # Return the default value if no other weight thresholds match
        return self.weight_thresholds['default']
    
    def get_times(self, pmi, uncertainty, date, time):
        """
        Calculates the time intervals based on PMI and uncertainty, returning the estimated time of event.
        """
        try:
            datetime_object = datetime.datetime.strptime(date + ' ' + time, '%Y-%m-%d %H:%M')
            pmi_delta = datetime.timedelta(hours=pmi)
            uncertainty_delta = datetime.timedelta(hours=uncertainty)

            time_calculated = datetime_object - pmi_delta
            time_plus_uncertainty = time_calculated + uncertainty_delta
            time_minus_uncertainty = time_calculated - uncertainty_delta

            return time_calculated, time_plus_uncertainty, time_minus_uncertainty
        except Exception as e:
            print("Error processing date/time: ", e)
            return None, None, None


if __name__ == '__main__':
    if len(sys.argv) < 8:
        print("Usage: python script.py <cover> <surfact> <t_rectum_c> <t_ambient_c> <body_wt_kg> <date> <time>")
        sys.exit(1)

    cover = sys.argv[1]
    surfact = sys.argv[2]
    try:
        t_rectum_c = float(sys.argv[3])
        t_ambient_c = float(sys.argv[4])
        body_wt_kg = float(sys.argv[5])
    except ValueError:
        print("Error: Temperature and body weight must be numbers.")
        sys.exit(1)

    date = sys.argv[6]
    time = sys.argv[7]

    calc = PMICalculator()
    pmi = calc.calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg)
    if isinstance(pmi, str):
        print(pmi)
    else:
        uncertainty = calc.get_uncertainty(t_ambient_c, body_wt_kg, pmi, cover, surfact)
        interval = calc.get_times(pmi, uncertainty, date, time)
        if interval[0]:
            print(f"Geschatte tijd van overlijden: {interval[0]} ({pmi} uur geleden)")
            print("Met onzekerheidsbereik: {} tot {}".format(interval[2], interval[1]))
