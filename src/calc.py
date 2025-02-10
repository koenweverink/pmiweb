import math
import sys
import datetime

class PMICalculator:
    def __init__(self):
        self.correction_factors_table = {
            4: [1.6, 2.1, 2.7, 3.5, 4.5, 5.7, 7.1, 8.8, 10.9],
            6: [1.6, 2.1, 2.7, 3.4, 4.3, 5.3, 6.6, 8.1, 9.8],
            8: [1.6, 2.0, 2.6, 3.3, 4.1, 5.0, 6.2, 7.5, 8.9],
            10: [1.6, 2.0, 2.5, 3.2, 3.9, 4.8, 5.8, 7.0, 8.3],
            20: [1.5, 1.9, 2.3, 2.8, 3.4, 4.0, 4.7, 5.5, 6.2],
            30: [1.4, 1.8, 2.2, 2.6, 3.0, 3.5, 4.0, 4.6, 5.1],
            40: [1.4, 1.6, 2.1, 2.5, 2.8, 3.2, 3.6, 3.9, 4.3],
            50: [1.4, 1.6, 2.0, 2.3, 2.6, 2.9, 3.2, 3.5, 3.8],
            60: [1.4, 1.6, 1.8, 2.0, 2.4, 2.7, 2.9, 3.2, 3.4],
            70: [1.3, 1.4, 1.6, 1.8, 2.0, 2.2, 2.4, 2.6, 2.8, 3.0],
            80: [1.4, 1.6, 1.8, 2.0, 2.1, 2.3, 2.5, 2.7, 2.8],
            90: [1.4, 1.6, 1.8, 1.8, 2.0, 2.2, 2.3, 2.5, 2.6],
            100: [1.4, 1.6, 1.5, 1.8, 1.9, 2.1, 2.2, 2.3, 2.4],
            110: [1.4, 1.4, 1.5, 1.7, 1.8, 1.9, 2.1, 2.2, 2.3],
            120: [1.3, 1.4, 1.6, 1.6, 1.7, 1.9, 2.0, 2.0, 2.1],
            130: [1.2, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 1.9, 2.0],
            140: [1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.8, 1.9],
            150: [1.2, 1.3, 1.4, 1.5, 1.6, 1.6, 1.7, 1.7, 1.9],
        }

        self.factors = {
            'Droge kleding en/of bedekking, stilstaande lucht': {
                'Naakt': 1.0,
                '1-2 dunne lagen': 1.1,
                '1-2 dikkere lagen': 1.2,
                '2-3 dunne lagen': 1.2,
                '3-4 dunne lagen': 1.3,
                'Meerdere dunne/dikkere lagen': 1.4,
                'Dik beddengoed': 1.8,
                'Dik beddengoed plus kleding': 2.4,
                'Zeer veel dikke lagen': 3.0,
            },
            'Droge kleding en/of bedekking, bewegende lucht': {
                'Naakt': 0.75,
                '1-2 dunne lagen': 0.9,
                '1-2 dikkere lagen': 0.9,
                '2-3 dunne lagen': 1.2,
                '3-4 dunne lagen': 1.3,
                'Meerdere dunne/dikkere lagen': 1.4,
                'Dik beddengoed': 1.8,
                'Dik beddengoed plus kleding': 2.4,
                'Zeer veel dikke lagen': 3.0,
            },
            'Natte kleding en/of bedekking, nat lichaamsoppervlak, stilstaande lucht': {
                'Naakt': 0.5,
                '1-2 dunne lagen': 0.8,
                '1-2 dikkere lagen': 1.1,
                '2-3 dunne lagen': 1.2,
                '3-4 dunne lagen': 1.2,
                'Meerdere dunne/dikkere lagen': 1.2,
                'Dik beddengoed': 1.2,
                'Dik beddengoed plus kleding': 1.2,
                'Zeer veel dikke lagen': 1.2,
            },
            'Natte kleding en/of bedekking, nat lichaamsoppervlak, bewegende lucht': {
                'Naakt': 0.7,
                '1-2 dunne lagen': 0.7,
                '1-2 dikkere lagen': 0.9,
                '2-3 dunne lagen': 0.9,
                '3-4 dunne lagen': 0.9,
                'Meerdere dunne/dikkere lagen': 0.9,
                'Dik beddengoed': 0.9,
                'Dik beddengoed plus kleding': 0.9,
                'Zeer veel dikke lagen': 0.9,
            },
            'Stilstaand water': {
                'Naakt': 0.5,
                '1-2 dunne lagen': 0.7,
                '1-2 dikkere lagen': 0.8,
                '2-3 dunne lagen': 0.9,
                '3-4 dunne lagen': 1.0,
                'Meerdere dunne/dikkere lagen': 1.0,
                'Dik beddengoed': 1.0,
                'Dik beddengoed plus kleding': 1.0,
                'Zeer veel dikke lagen': 1.0,
            },
            'Stromend water': {
                'Naakt': 0.35,
                '1-2 dunne lagen': 0.5,
                '1-2 dikkere lagen': 0.7,
                '2-3 dunne lagen': 0.8,
                '3-4 dunne lagen': 0.9,
                'Meerdere dunne/dikkere lagen': 1.0,
                'Dik beddengoed': 1.0,
                'Dik beddengoed plus kleding': 1.0,
                'Zeer veel dikke lagen': 1.0,
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

    def adjust_correction_factor(self, cf, body_wt_kg):
        weight = round(body_wt_kg, -1)

        if weight == 70 or cf < 1.4:
            return cf

        cf_row_70kg = self.correction_factors_table[70]

        try:
            index = cf_row_70kg.index(cf)
            return self.correction_factors_table[weight][index]
        except ValueError:
            lower_cf, upper_cf = self.get_nearest_factors(cf, cf_row_70kg)
            lower_index = cf_row_70kg.index(lower_cf)
            upper_index = cf_row_70kg.index(upper_cf)
            lower_cf_weight = self.correction_factors_table[weight][lower_index]
            upper_cf_weight = self.correction_factors_table[weight][upper_index]
            return self.linear_interpolate(cf, lower_cf, upper_cf, lower_cf_weight, upper_cf_weight)


    def get_nearest_factors(self, target, values):
        """Finds the two nearest values in the list 'values' to the given 'target' value."""
        sorted_values = sorted(values)
        lower = max([v for v in sorted_values if v <= target], default=sorted_values[0])
        upper = min([v for v in sorted_values if v >= target], default=sorted_values[-1])
        return lower, upper
    
    def linear_interpolate(self, x, x0, x1, y0, y1):
        """Performs linear interpolation to estimate y at point x, given points (x0, y0) and (x1, y1)."""
        if x1 == x0:
            return y0  # Avoid division by zero, should not happen in normal cases
        return y0 + (y1 - y0) * (x - x0) / (x1 - x0)
    

    def calc_pmi(self, cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg, underlay):
        self.cover = cover
        self.surfacet = surfact
        self.t_rectum_c = t_rectum_c
        self.t_ambient_c = t_ambient_c
        self.body_wt_kg = body_wt_kg
        self.underlay = underlay
        
        if t_ambient_c > t_rectum_c:
            return "Error: De lichaamstemperatuur is lager dan de omgevingstemperatuur"

        if body_wt_kg < 11:
            return "Error: Er is een hoge mate van onzekerheid door het lage lichaamsgewicht."

        corrective_factor = self.get_corrective_factor(cover, surfact, underlay)

        if corrective_factor:
            adjusted_cf = self.adjust_correction_factor(corrective_factor, body_wt_kg)

        if adjusted_cf == 0:
            return "Error: Correctiefactor is 0."

        left_side = (t_rectum_c - t_ambient_c) / (37.2 - t_ambient_c)
        bigB = (-1.2815 * (adjusted_cf * body_wt_kg) ** -0.625 + 0.0284)

        best_time = 0.0
        while best_time < 100:  # up to 100 hours
            if abs(left_side - self.get_right_side(t_ambient_c, bigB, best_time)) < abs(left_side - self.get_right_side(t_ambient_c, bigB, best_time + 0.1)):
                break
            best_time += 0.1

        self.best_time = (math.ceil(best_time * 10) / 10)

        uncertainty = self.get_uncertainty(t_ambient_c, body_wt_kg, best_time, cover, surfact)
        if uncertainty == 69:
            return "Error: Er is een hoge mate van onzekerheid."

        return int(round(best_time * 60))  # convert hours back to minutes

    def get_right_side(self, t_ambient_c, bigB, f):
        if t_ambient_c <= 23:
            return 1.25 * math.exp(bigB * f) - 0.25 * math.exp(5 * bigB * f)
        else:
            return 1.11 * math.exp(bigB * f) - 0.11 * math.exp(10 * bigB * f)

    def get_corrective_factor(self, cover, surfact, underlay):
        base_factor = self.factors.get(surfact, {}).get(cover, None)
        
        if base_factor is None:
            raise ValueError(f"No correction factor available for cover '{cover}' with surface type '{surfact}'.")

        # Default adjustment is 0 unless specified otherwise
        adjustment = 0.0  

        if underlay == "Willekeurig: Vloer binnenshuis, grasveld, droge aarde, asfalt":
            adjustment = 0.0
        elif underlay == 'Zware vulling':
            adjustment = 0.3 if cover in ['1-2 dunne lagen'] else 0.1
        elif underlay == 'Matras, dik tapijt of vloerkleed':
            adjustment = 0.15 if cover == 'Naakt' else 0.1
        elif underlay == 'Beton, steen, tegels':
            adjustment = -0.75 if cover == 'Naakt' else -0.2

        return round(base_factor + adjustment, 1)  # Ensure proper rounding



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
        return self.weight_thresholds['default']

    def get_times(self, pmi, uncertainty, date, time):
        try:
            datetime_object = datetime.datetime.strptime(date + ' ' + time, '%Y-%m-%d %H:%M')
            pmi_delta = datetime.timedelta(minutes=pmi)
            uncertainty_delta = datetime.timedelta(minutes=uncertainty)

            time_calculated = datetime_object - pmi_delta
            time_plus_uncertainty = time_calculated + uncertainty_delta
            time_minus_uncertainty = time_calculated - uncertainty_delta

            return time_calculated, time_plus_uncertainty, time_minus_uncertainty
        except Exception as e:
            print("Error processing date/time: ", e)
            return None, None, None

if __name__ == '__main__':
    if len(sys.argv) < 9:
        print("Usage: python script.py <cover> <surfact> <t_rectum_c> <t_ambient_c> <body_wt_kg> <date> <time> <underlay>")
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
    underlay = sys.argv[8]

    calc = PMICalculator()
    pmi = calc.calc_pmi(cover, surfact, t_rectum_c, t_ambient_c, body_wt_kg, underlay)
    if isinstance(pmi, str):
        print(pmi)
    else:
        uncertainty = calc.get_uncertainty(t_ambient_c, body_wt_kg, pmi, cover, surfact)
        interval = calc.get_times(pmi, uncertainty, date, time)
        if interval[0]:
            print(f"Geschatte tijd van overlijden: {interval[0]} ({pmi} minuten geleden)")
            print("Met onzekerheidsbereik: {} tot {}".format(interval[2], interval[1]))
            B_value = -1.2815 * (calc.get_corrective_factor(cover, surfact, underlay) * body_wt_kg) ** -0.625 + 0.0284
            print(f"B: {B_value}")
            print(f"T_R: {t_rectum_c}")
            print(f"T_O: {t_ambient_c}")
            print(f"Correctiefactor: {calc.get_corrective_factor(cover, surfact, underlay)}")
            print(f"Lichaamsgewicht: {body_wt_kg}")
            print(f"Formula: {'below' if t_rectum_c <= 23 else 'above'}")
